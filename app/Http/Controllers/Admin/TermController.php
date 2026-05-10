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

        return response()->json([
            'success' => true,
            'term' => $term,
            'message' => 'Term created successfully'
        ]);
    }

    public function index($type)
    {
        $terms = Term::where('type', $type)
            ->where(function($query) {
                if (tenant()) {
                    $query->where('tenant_id', tenant()->id);
                }
            })
            ->get();

        return response()->json([
            'success' => true,
            'terms' => $terms
        ]);
    }
}
