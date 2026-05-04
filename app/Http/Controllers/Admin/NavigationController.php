<?php

namespace App\Http\Controllers\Admin;

use App\Models\Navigation;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NavigationController extends Controller
{
    /**
     * Display a listing of navigation items for the current tenant.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;

        $navigations = Navigation::where('tenant_id', $tenantId)
            ->ordered()
            ->paginate(20);

        return view('admin.navigation.index', compact('navigations'));
    }

    /**
     * Show the form for creating a new navigation item.
     */
    public function create()
    {
        return view('admin.navigation.create');
    }

    /**
     * Store a newly created navigation item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'url' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = $request->has('is_active');

        Navigation::create($validated);

        return redirect()->route('admin.navigation.index')
            ->with('success', 'Navigation item created successfully.');
    }

    /**
     * Show the form for editing the specified navigation item.
     */
    public function edit(Navigation $navigation)
    {
        $this->authorize('update', $navigation);
        return view('admin.navigation.edit', compact('navigation'));
    }

    /**
     * Update the specified navigation item in storage.
     */
    public function update(Request $request, Navigation $navigation)
    {
        $this->authorize('update', $navigation);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'url' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $navigation->update($validated);

        return redirect()->route('admin.navigation.index')
            ->with('success', 'Navigation item updated successfully.');
    }

    /**
     * Remove the specified navigation item from storage.
     */
    public function destroy(Navigation $navigation)
    {
        $this->authorize('delete', $navigation);
        $navigation->delete();

        return redirect()->route('admin.navigation.index')
            ->with('success', 'Navigation item deleted successfully.');
    }

    /**
     * Update the order of navigation items (AJAX).
     */
    public function updateOrder(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $items = $request->validate(['items' => 'required|array']);

        foreach ($items['items'] as $order => $id) {
            Navigation::where('tenant_id', $tenantId)
                ->where('id', $id)
                ->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}
