<?php

namespace App\Services;

use App\Models\PageModule;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

class PageModuleService
{
    /**
     * Get all modules for current tenant
     */
    public function getModulesForTenant($tenantId = null, $paginate = true)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            return $paginate ? PageModule::paginate(20) : collect();
        }

        $query = PageModule::forTenant($tenantId)->ordered();

        return $paginate ? $query->paginate(20) : $query->get();
    }

    /**
     * Get modules by type
     */
    public function getModulesByType($type, $tenantId = null, $published = false)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            return collect();
        }

        $query = PageModule::forTenant($tenantId)->byType($type);

        if ($published) {
            $query->published()->active();
        }

        return $query->ordered()->get();
    }

    /**
     * Get module by slug
     */
    public function getModuleBySlug($slug, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            return null;
        }

        return PageModule::forTenant($tenantId)->bySlug($slug)->first();
    }

    /**
     * Create new module
     */
    public function createModule(array $data, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            throw new \Exception('Tenant not found');
        }

        // Auto-generate slug
        $data['slug'] = $data['slug'] ?? Str::slug($data['title'] ?? 'page');
        
        // Ensure unique slug for this tenant
        $baseSlug = $data['slug'];
        $count = PageModule::forTenant($tenantId)->bySlug($data['slug'])->count();
        if ($count > 0) {
            $data['slug'] = $baseSlug . '-' . ($count + 1);
        }

        $data['tenant_id'] = $tenantId;

        return PageModule::create($data);
    }

    /**
     * Update module
     */
    public function updateModule($moduleId, array $data, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        $module = PageModule::forTenant($tenantId)->findOrFail($moduleId);

        // Handle slug update with uniqueness check
        if (isset($data['slug']) && $data['slug'] !== $module->slug) {
            $newSlug = $data['slug'];
            $baseSlug = $newSlug;
            $count = PageModule::forTenant($tenantId)
                ->bySlug($newSlug)
                ->where('id', '!=', $moduleId)
                ->count();
            if ($count > 0) {
                $data['slug'] = $baseSlug . '-' . ($count + 1);
            }
        }

        $module->update($data);

        return $module;
    }

    /**
     * Delete module
     */
    public function deleteModule($moduleId, $tenantId = null, $force = false)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        $module = PageModule::forTenant($tenantId)->findOrFail($moduleId);

        return $force ? $module->forceDelete() : $module->delete();
    }

    /**
     * Publish/Draft module
     */
    public function publishModule($moduleId, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        $module = PageModule::forTenant($tenantId)->findOrFail($moduleId);
        $module->update(['status' => 'published']);

        return $module;
    }

    public function draftModule($moduleId, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        $module = PageModule::forTenant($tenantId)->findOrFail($moduleId);
        $module->update(['status' => 'draft']);

        return $module;
    }

    /**
     * Toggle active status
     */
    public function toggleActive($moduleId, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        $module = PageModule::forTenant($tenantId)->findOrFail($moduleId);
        $module->update(['is_active' => !$module->is_active]);

        return $module;
    }

    /**
     * Update visibility
     */
    public function updateVisibility($moduleId, $showToGuest = true, $showToCustomer = true, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        $module = PageModule::forTenant($tenantId)->findOrFail($moduleId);
        $module->update([
            'show_to_guest' => $showToGuest,
            'show_to_customer' => $showToCustomer,
        ]);

        return $module;
    }

    /**
     * Get module statistics
     */
    public function getStats($tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            return [];
        }

        return [
            'total' => PageModule::forTenant($tenantId)->count(),
            'published' => PageModule::forTenant($tenantId)->published()->count(),
            'draft' => PageModule::forTenant($tenantId)->where('status', 'draft')->count(),
            'navbar' => PageModule::forTenant($tenantId)->byType('navbar')->count(),
            'blog' => PageModule::forTenant($tenantId)->byType('blog')->count(),
            'sidebar' => PageModule::forTenant($tenantId)->byType('sidebar')->count(),
        ];
    }

    /**
     * Get featured content for homepage
     */
    public function getFeaturedContent($type = null, $limit = 5, $tenantId = null)
    {
        $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            return collect();
        }

        $query = PageModule::forTenant($tenantId)->published()->active();

        if ($type) {
            $query->byType($type);
        }

        return $query->limit($limit)->ordered()->get();
    }

    /**
     * Handle null/missing data gracefully
     */
    public function safeGetModule($moduleId, $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? session('tenant_id') ?? auth()->user()?->tenant_id;
            return PageModule::forTenant($tenantId)->find($moduleId);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
