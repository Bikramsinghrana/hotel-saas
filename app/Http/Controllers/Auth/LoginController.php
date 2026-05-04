<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('auth.login', ['isSeller' => false]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($this->authService->login($data)) {
            return redirect()->intended(route('customer.dashboard', [], false) ?: '/customer/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function showSellerLoginForm()
    {
        return view('auth.login', ['isSeller' => true]);
    }

    public function sellerLogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($this->authService->login($data)) {
            $user = \Illuminate\Support\Facades\Auth::user();

            // If they are a pure customer trying to use the seller login, kick them out
            if ($user && $user->hasRole(\App\Enums\RoleEnum::CUSTOMER->value) && $user->roles->count() === 1) {
                return redirect()->intended(route('customer.dashboard', [], false) ?: '/customer/dashboard');
            }

            // ── KEY FIX ──────────────────────────────────────────────────────────
            // Store the tenant_id in session right after login so that tenant()
            // helper works correctly on every subsequent request in the admin panel.
            if ($user && $user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            }
            // ─────────────────────────────────────────────────────────────────────

            return redirect()->intended(route('admin.dashboard', [], false) ?: '/admin/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
