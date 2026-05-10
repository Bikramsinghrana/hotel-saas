<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TermController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'price' => 'nullable|numeric',
            'price_type' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $term = Term::create([
            'tenant_id' => tenant() ? tenant()->id : null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'type' => $validated['type'],
            'price' => $validated['price'] ?? 0,
            'price_type' => $validated['price_type'] ?? '$',
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'term' => $term,
                'message' => 'Term created successfully'
            ]);
        }

        return back()->with('success', 'Term created successfully');
    }

    public function index(Request $request, $type = null)
    {
        $type = $type ?? $request->get('type');
        
        $query = Term::where('type', $type)
            ->where(function($query) {
                if (tenant()) {
                    $query->where('tenant_id', tenant()->id);
                }
            })
            ->latest();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'terms' => $query->get()
            ]);
        }

        $terms = $query->paginate(15);
        return view('themes.hotel.admin.terms.index', compact('terms', 'type'));
    }

    public function update(Request $request, $id)
    {
        $term = Term::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'price_type' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $term->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'term' => $term,
                'message' => 'Term updated successfully'
            ]);
        }

        return back()->with('success', 'Term updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $term = Term::findOrFail($id);
        $term->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Term deleted successfully'
            ]);
        }

        return back()->with('success', 'Term deleted successfully');
    }
}
