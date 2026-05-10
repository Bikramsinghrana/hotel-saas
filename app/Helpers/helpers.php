<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (! function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        // Priority 1: session (most reliable after login sets it)
        if (session()->has('tenant_id')) {
            return Tenant::find(session('tenant_id'));
        }

        // Priority 2: authenticated user's tenant_id (handles cases where session wasn't set)
        if (auth()->check() && auth()->user()->tenant_id) {
            $t = Tenant::find(auth()->user()->tenant_id);
            if ($t) {
                // Backfill the session so subsequent requests are fast
                session(['tenant_id' => $t->id]);
                return $t;
            }
        }

        // Priority 3: service container (set by a TenantMiddleware, if present)
        if (app()->bound('tenant')) {
            return app('tenant');
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
