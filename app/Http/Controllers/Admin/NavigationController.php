<?php

namespace App\Http\Controllers\Admin;

use App\Models\Navigation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\ModuleStatusEnum;
use Illuminate\Support\Str;

class NavigationController extends Controller
{
    public function index()
    {
        $navigations = Navigation::where('tenant_id', tenant()->id)
            ->orderBy('order', 'asc')
            ->paginate(20);

        $stats = [
            'total' => Navigation::where('tenant_id', tenant()->id)->count(),
            'published' => Navigation::where('tenant_id', tenant()->id)->where('status', ModuleStatusEnum::PUBLISHED)->count(),
            'draft' => Navigation::where('tenant_id', tenant()->id)->where('status', ModuleStatusEnum::DRAFT)->count(),
        ];

        return view('admin.navigations.index', compact('navigations', 'stats'));
    }

    public function create()
    {
        return view('admin.navigations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $validated['tenant_id'] = tenant()->id;
        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_active'] = $request->has('is_active');

        Navigation::create($validated);

        return redirect()->route('admin.navigations.index')->with('success', 'Navigation item created successfully.');
    }

    public function edit(Navigation $navigation)
    {
        return view('admin.navigations.edit', compact('navigation'));
    }

    public function update(Request $request, Navigation $navigation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:navigations,slug,' . $navigation->id . ',id,tenant_id,' . tenant()->id,
            'url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_active'] = $request->has('is_active');

        $navigation->update($validated);

        return redirect()->route('admin.navigations.index')->with('success', 'Navigation item updated successfully.');
    }

    public function destroy(Navigation $navigation)
    {
        $navigation->delete();
        return redirect()->route('admin.navigations.index')->with('success', 'Navigation item deleted successfully.');
    }

    public function toggleActive(Navigation $navigation)
    {
        $navigation->update(['is_active' => !$navigation->is_active]);
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
