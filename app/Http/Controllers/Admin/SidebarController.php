<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sidebar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\ModuleStatusEnum;
use Illuminate\Support\Str;

class SidebarController extends Controller
{
    public function index()
    {
        $sidebars = Sidebar::where('tenant_id', tenant()->id)
            ->orderBy('order', 'asc')
            ->paginate(20);

        $stats = [
            'total' => Sidebar::where('tenant_id', tenant()->id)->count(),
            'published' => Sidebar::where('tenant_id', tenant()->id)->where('status', ModuleStatusEnum::PUBLISHED)->count(),
            'draft' => Sidebar::where('tenant_id', tenant()->id)->where('status', ModuleStatusEnum::DRAFT)->count(),
        ];

        return view('admin.sidebars.index', compact('sidebars', 'stats'));
    }

    public function create()
    {
        return view('admin.sidebars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $validated['tenant_id'] = tenant()->id;
        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_active'] = $request->has('is_active');

        Sidebar::create($validated);

        return redirect()->route('admin.sidebars.index')->with('success', 'Sidebar module created successfully.');
    }

    public function edit(Sidebar $sidebar)
    {
        return view('admin.sidebars.edit', compact('sidebar'));
    }

    public function update(Request $request, Sidebar $sidebar)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sidebars,slug,' . $sidebar->id . ',id,tenant_id,' . tenant()->id,
            'content' => 'nullable|string',
            'status' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_active'] = $request->has('is_active');

        $sidebar->update($validated);

        return redirect()->route('admin.sidebars.index')->with('success', 'Sidebar module updated successfully.');
    }

    public function destroy(Sidebar $sidebar)
    {
        $sidebar->delete();
        return redirect()->route('admin.sidebars.index')->with('success', 'Sidebar module deleted successfully.');
    }

    public function toggleActive(Sidebar $sidebar)
    {
        $sidebar->update(['is_active' => !$sidebar->is_active]);
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
