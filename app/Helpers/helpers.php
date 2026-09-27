<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (! function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        try {
            // Priority 1: session (most reliable after login sets it)
            if (app()->bound('session') && session()->has('tenant_id')) {
                return Tenant::with(['theme', 'subTheme'])->find(session('tenant_id'));
            }

            // Priority 2: authenticated user's tenant_id (handles cases where session wasn't set)
            if (app()->bound('auth') && auth()->guard()->hasUser() && auth()->user()->tenant_id) {
                $t = Tenant::with(['theme', 'subTheme'])->find(auth()->user()->tenant_id);
                if ($t) {
                    if (app()->bound('session')) {
                        session(['tenant_id' => $t->id]);
                    }
                    return $t;
                }
            }

            // Priority 3: service container (set by a TenantMiddleware, if present)
            if (app()->bound('tenant')) {
                return app('tenant');
            }
        } catch (\Throwable $e) {
            // Fallback gracefully in non-HTTP/CLI environments
        }

        return null;
    }
}


if (! function_exists('isSingleHotel')) {
    function isSingleHotel(): bool
    {
        $t = tenant();
        return $t && $t->subTheme && $t->subTheme->type === 'single_hotel';
    }
}

if (! function_exists('themeView')) {
    function themeView(string $view, array $data = [])
    {
        $theme = config('app.theme') ?? session('theme');
        if ($theme && view()->exists("themes.$theme.$view")) {
            return view("themes.$theme.$view", $data);
        }
        return view($view, $data);
    }
}

if (! function_exists('uploadFile')) {
    function uploadFile($file, $path = 'uploads', $disk = 'public')
    {
        return $file->store($path, $disk);
    }
}

if (! function_exists('cacheRemember')) {
    function cacheRemember(string $key, $ttl, callable $cb)
    {
        return Cache::remember($key, $ttl, $cb);
    }
}

if (! function_exists('uploadImage')) {
    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder Relative path inside base_path('uploads')
     * @param int|null $width
     * @param int|null $height
     * @return string Final path relative to base_path()
     */


    // function uploadImage($file, $folder = 'hotel', $width = null, $height = null)
    // {
    //     $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

    //     $basePath = base_path('uploads/' . $folder);
    //     if (!file_exists($basePath)) {
    //         mkdir($basePath, 0775, true);
    //     }

    //     $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //     $fullPath = $basePath . '/' . $fileName;

    //     $image = $manager->read($file);

    //     if ($width && $height) {
    //         $image->cover($width, $height);
    //     }

    //     $image->save($fullPath);

    //     return 'uploads/' . $folder . '/' . $fileName;
    // }


    function uploadImage($file, $folder = 'hotel', $width = null, $height = null)
    {
        $manager = new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $image = $manager->read($file);

        if ($width && $height) {
            $image->cover($width, $height);
        }

        // temp file
        $tempPath = storage_path('app/temp_' . $fileName);
        $image->save($tempPath);

        // folder path
        $folderPath = 'uploads/' . $folder;

        // create folder if not exists
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }

        // store in public disk
        $path = Storage::disk('public')->putFileAs(
            $folderPath,
            new \Illuminate\Http\File($tempPath),
            $fileName
        );

        unlink($tempPath);

        return $path; // uploads/hotel/xxx.jpg
    }
    
}


if (!function_exists('imageUrl')) {

    function imageUrl($path = null)
    {
        $default = asset('assets/images/dummy-image.png');

        if (!$path) {
            return $default;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (!Storage::disk('public')->exists($path)) {
            return $default;
        }

        return Storage::disk('public')->url($path);
    }
}


if (!function_exists('deleteFile')) {

    function deleteFile($path, $disk = 'public')
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin($user = null): bool
    {
        $u = $user ?? auth()->user();
        return (bool) ($u && $u->hasRole(\App\Enums\RoleEnum::SUPER_ADMIN->value));
    }
}

if (!function_exists('can_action')) {
    function can_action(string $permission, $user = null): bool
    {
        $u = $user ?? auth()->user();
        if (!$u) {
            return false;
        }

        // Super Admin has all permissions implicitly
        if (is_super_admin($u)) {
            return true;
        }

        try {
            if ($u->hasPermissionTo($permission)) {
                return true;
            }
        } catch (\Throwable $e) {
            // Permission might not exist in database table
        }

        try {
            return (bool) $u->can($permission);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('authorize_action')) {
    function authorize_action(string $permission, $user = null): void
    {
        if (!can_action($permission, $user)) {
            abort(403, "You do not have permission to execute '{$permission}'.");
        }
    }
}

if (!function_exists('has_feature')) {
    function has_feature(string $featureKey, ?Tenant $tenant = null): bool
    {
        if (is_super_admin()) {
            return true;
        }
        $tenant = $tenant ?? tenant();
        return app(\App\Services\TenantAccessService::class)->canAccessFeature($tenant, $featureKey);
    }
}

if (!function_exists('feature_limit')) {
    function feature_limit(string $featureKey, ?Tenant $tenant = null): ?string
    {
        $tenant = $tenant ?? tenant();
        return app(\App\Services\TenantAccessService::class)->getFeatureLimit($tenant, $featureKey);
    }
}

if (!function_exists('can_use_subtheme')) {
    function can_use_subtheme($subTheme, ?Tenant $tenant = null): bool
    {
        if (is_super_admin()) {
            return true;
        }
        $tenant = $tenant ?? tenant();
        return app(\App\Services\TenantAccessService::class)->canAccessSubTheme($tenant, $subTheme);
    }
}

if (!function_exists('can_use_theme')) {
    function can_use_theme($theme, ?Tenant $tenant = null): bool
    {
        if (is_super_admin()) {
            return true;
        }
        $tenant = $tenant ?? tenant();
        return app(\App\Services\TenantAccessService::class)->canAccessTheme($tenant, $theme);
    }
}

if (!function_exists('can_manage_theme')) {
    function can_manage_theme($theme, ?Tenant $tenant = null): bool
    {
        return is_super_admin() || can_use_theme($theme, $tenant);
    }
}

if (!function_exists('can_manage_subtheme')) {
    function can_manage_subtheme($subTheme, ?Tenant $tenant = null): bool
    {
        return is_super_admin() || can_use_subtheme($subTheme, $tenant);
    }
}

if (!function_exists('option')) {
    /**
     * Get dynamic option value with hierarchical fallback
     */
    function option(string $key, $default = null, $tenantId = null, $hotelId = null)
    {
        return app(\App\Services\OptionService::class)->get($key, $default, $tenantId, $hotelId);
    }
}

if (!function_exists('option_set')) {
    /**
     * Set or update dynamic option value
     */
    function option_set(string $key, $value, string $group = 'general', $type = null, $tenantId = null, $hotelId = null, array $extra = [])
    {
        return app(\App\Services\OptionService::class)->set($key, $value, $group, $type, $tenantId, $hotelId, $extra);
    }
}

if (!function_exists('option_group')) {
    /**
     * Get all dynamic options in a group
     */
    function option_group(string $group, $tenantId = null, $hotelId = null): array
    {
        return app(\App\Services\OptionService::class)->getGroup($group, $tenantId, $hotelId);
    }
}

if (!function_exists('validate_coupon')) {
    /**
     * Helper function to check coupon validity across dates, days of week, time slots, min spend, and limits.
     *
     * @param string|\App\Models\Coupon $couponOrCode
     * @param int|null $hotelId
     * @param int|null $tenantId
     * @param string|\Carbon\Carbon|null $date
     * @param string|\Carbon\Carbon|null $time
     * @param float $amount
     * @return array ['success' => bool, 'message' => string, 'coupon' => Coupon|null]
     */
    function validate_coupon($couponOrCode, $hotelId = null, $tenantId = null, $date = null, $time = null, $amount = 0): array
    {
        return app(\App\Services\CouponService::class)->validate($couponOrCode, $hotelId, $tenantId, $date, $time, $amount);
    }
}

if (!function_exists('is_coupon_valid')) {
    /**
     * Quick boolean check if a coupon is valid for a given date/time/hotel.
     */
    function is_coupon_valid($couponOrCode, $hotelId = null, $tenantId = null, $date = null, $time = null, $amount = 0): bool
    {
        $res = validate_coupon($couponOrCode, $hotelId, $tenantId, $date, $time, $amount);
        return !empty($res['success']);
    }
}




