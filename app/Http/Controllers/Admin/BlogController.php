<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\ModuleStatusEnum;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('tenant_id', tenant()->id)
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Blog::where('tenant_id', tenant()->id)->count(),
            'published' => Blog::where('tenant_id', tenant()->id)->where('status', ModuleStatusEnum::PUBLISHED)->count(),
            'draft' => Blog::where('tenant_id', tenant()->id)->where('status', ModuleStatusEnum::DRAFT)->count(),
        ];

        return view('admin.blogs.index', compact('blogs', 'stats'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = tenant()->id;
        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        Blog::create($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $blog->id . ',id,tenant_id,' . tenant()->id,
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
    }

    public function toggleActive(Blog $blog)
    {
        $blog->update(['is_active' => !$blog->is_active]);
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
