<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        @extends('layouts.app')

        @section('content')
            <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
                <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" />
                <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                    <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                        <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                            <div class="flex lg:justify-center lg:col-start-2">
                                <svg class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z" fill="currentColor"/></svg>
                        </header>

                        <main class="mt-6">
                            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                                <!-- left cards and content retained from original welcome -->
                                <a href="https://laravel.com/docs" id="docs-card" class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                                    <div id="screenshot-container" class="relative flex w-full flex-1 items-stretch">
                                        <img src="https://laravel.com/assets/img/welcome/docs-light.svg" alt="Laravel documentation screenshot" class="aspect-video h-full w-full flex-1 rounded-[10px] object-top object-cover drop-shadow-[0px_4px_34px_rgba(0,0,0,0.06)] dark:hidden" />
                                    </div>

                                    <div class="relative flex items-center gap-6 lg:items-end">
                                        <div id="docs-card-content" class="flex items-start gap-6 lg:flex-col">
                                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                                                <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path fill="#FF2D20" d="M23 4a1 1 0 0 0-1.447-.894L12.224 7.77a.5.5 0 0 1-.448 0L2.447 3.106A1 1 0 0 0 1 4v13.382a1.99 1.99 0 0 0 1.105 1.79l9.448 4.728c.14.065.293.1.447.1.154-.005.306-.04.447-.105l9.453-4.724a1.99 1.99 0 0 0 1.1-1.789V4ZM3 6.023a.25.25 0 0 1 .362-.223l7.5 3.75a.251.251 0 0 1 .138.223v11.2a.25.25 0 0 1-.362.224l-7.5-3.75a.25.25 0 0 1-.138-.22V6.023Zm18 11.2a.25.25 0 0 1-.138.224l-7.5 3.75a.249.249 0 0 1-.329-.099.249.249 0 0 1-.033-.12V9.772a.251.251 0 0 1 .138-.224l7.5-3.75a.25.25 0 0 1 .362.224v11.2Z"/></svg>
                                            </div>

                                            <div class="pt-3 sm:pt-5 lg:pt-0">
                                                <h2 class="text-xl font-semibold text-black dark:text-white">Documentation</h2>

                                                <p class="mt-4 text-sm/relaxed">
                                                    Laravel has wonderful documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience with Laravel, we recommend reading our documentation from beginning to end.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- other cards omitted for brevity in this simplified layout -->
                            </div>
                        </main>

                        <!-- hotels / hero area remains as before (refactored to use layout) -->
                        <section class="py-12 px-6 bg-white/50 dark:bg-black/60 rounded-lg mt-8" id="hotels">
                            <div class="max-w-7xl mx-auto">
                                <h2 class="text-3xl font-bold text-center">Discover & Book Great Hotels</h2>
                                <p class="text-center mt-2 text-black/70 dark:text-white/70">Book hotels, compare prices, read reviews — demo version.</p>

                                @php
                                    $hotels = collect();
                                    if (class_exists(\App\Models\Hotel::class)) {
                                        try { $hotels = \App\Models\Hotel::where('status','active')->limit(6)->get(); } catch (\Throwable $e) { $hotels = collect(); }
                                    }
                                    if ($hotels->isEmpty()) {
                                        $hotels = collect([
                                            (object)['name'=>'Seaside Resort','address'=>['city'=>'Beachville'],'rating'=>4.5,'base_price'=>149.00,'media'=>collect([(object)['path'=>'https://images.unsplash.com/photo-1501117716987-c8e42d5b3e1b?w=800&h=600&fit=crop']])],
                                            (object)['name'=>'City Center Hotel','address'=>['city'=>'Metro City'],'rating'=>4.0,'base_price'=>99.00,'media'=>collect([(object)['path'=>'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c0?w=800&h=600&fit=crop']])],
                                        ]);
                                    }
                                @endphp

                                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach($hotels as $hotel)
                                        @php
                                            $imgPath = optional(optional($hotel->media)->first())->path ?? (is_array($hotel->media) && isset($hotel->media[0]['path']) ? $hotel->media[0]['path'] : null);
                                            $isExternal = $imgPath && strpos($imgPath,'http')===0;
                                            $imgSrc = $imgPath ? ($isExternal ? $imgPath : asset('storage/' . ltrim($imgPath,'/'))) : 'https://via.placeholder.com/800x600?text=Hotel+Image';
                                        @endphp

                                        <div class="border rounded-lg p-4">
                                            <div class="h-36 bg-gray-100 rounded-md overflow-hidden">
                                                <img src="{{ $imgSrc }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <h3 class="mt-3 font-semibold">{{ $hotel->name }}</h3>
                                            <p class="text-sm text-black/60">{{ is_array($hotel->address) ? ($hotel->address['city'] ?? '') : (optional($hotel->address)->city ?? '') }} · {{ $hotel->rating }}★</p>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-lg font-bold">${{ number_format($hotel->base_price,2) }}</div>
                                                <a href="#" class="text-sm text-blue-600">View</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        @endsection

                                    <p class="mt-4 text-sm/relaxed">
                                        Laravel's robust library of first-party tools and libraries, such as <a href="https://forge.laravel.com" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white dark:focus-visible:ring-[#FF2D20]">Forge</a>, <a href="https://vapor.laravel.com" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Vapor</a>, <a href="https://nova.laravel.com" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Nova</a>, <a href="https://envoyer.io" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Envoyer</a>, and <a href="https://herd.laravel.com" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Herd</a> help you take your projects to the next level. Pair them with powerful open source libraries like <a href="https://laravel.com/docs/billing" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Cashier</a>, <a href="https://laravel.com/docs/dusk" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Dusk</a>, <a href="https://laravel.com/docs/broadcasting" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Echo</a>, <a href="https://laravel.com/docs/horizon" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Horizon</a>, <a href="https://laravel.com/docs/sanctum" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Sanctum</a>, <a href="https://laravel.com/docs/telescope" class="rounded-sm underline hover:text-black focus:outline-none focus-visible:ring-1 focus-visible:ring-[#FF2D20] dark:hover:text-white">Telescope</a>, and more.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </main>

                    <section class="py-12 px-6 bg-white/50 dark:bg-black/60 rounded-lg mt-8">
                        <div class="max-w-7xl mx-auto">
                            <h2 class="text-3xl font-bold text-center">Discover & Book Great Hotels</h2>
                            <p class="text-center mt-2 text-black/70 dark:text-white/70">Book hotels, compare prices, read reviews — demo version.</p>

                            @php
                                if (! isset($hotels)) {
                                    $hotels = collect();
                                    $useDummy = false;

                                    if (class_exists(\App\Models\Hotel::class)) {
                                        try {
                                            $hotels = \App\Models\Hotel::where('status','active')->limit(6)->get();
                                        } catch (\Throwable $e) {
                                            $hotels = collect();
                                        }
                                    }

                                    if ($hotels->isEmpty()) {
                                        $useDummy = true;
                                        $hotels = collect([
                                            (object)['name' => 'Seaside Resort', 'address' => ['city' => 'Beachville'], 'rating' => 4.5, 'base_price' => 149.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1501117716987-c8e42d5b3e1b?w=800&h=600&fit=crop']])],
                                            (object)['name' => 'City Center Hotel', 'address' => ['city' => 'Metro City'], 'rating' => 4.0, 'base_price' => 99.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c0?w=800&h=600&fit=crop']])],
                                            (object)['name' => 'Mountain Lodge', 'address' => ['city' => 'Highpeak'], 'rating' => 4.7, 'base_price' => 179.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1501117716987-c8e42d5b3e1b?w=800&h=600&fit=crop&sig=2']])],
                                            (object)['name' => 'Urban Boutique', 'address' => ['city' => 'Downtown'], 'rating' => 4.3, 'base_price' => 129.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&h=600&fit=crop']])],
                                            (object)['name' => 'Country Inn', 'address' => ['city' => 'Riverside'], 'rating' => 4.1, 'base_price' => 89.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1505691723518-34a2c7b7a4b9?w=800&h=600&fit=crop']])],
                                            (object)['name' => 'Lakeview Hotel', 'address' => ['city' => 'Lakeside'], 'rating' => 4.6, 'base_price' => 159.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&h=600&fit=crop']])],
                                        ]);
                                    }
                                }
                            @endphp

                            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($hotels as $hotel)
                                    @php
                                        $imgPath = null;
                                        try {
                                            if (is_string($hotel->media ?? null)) {
                                                $imgPath = $hotel->media;
                                            } elseif (is_object($hotel) && method_exists($hotel, 'media')) {
                                                $m = optional($hotel->media->first());
                                                $imgPath = $m->path ?? null;
                                            } elseif (!empty($hotel->media) && is_iterable($hotel->media)) {
                                                $m = is_array($hotel->media) ? reset($hotel->media) : (is_object($hotel->media) ? (method_exists($hotel->media, 'first') ? optional($hotel->media->first()) : reset($hotel->media)) : null);
                                                $imgPath = $m->path ?? ($m['path'] ?? null ?? null);
                                            }
                                        } catch (\Throwable $e) {
                                            $imgPath = null;
                                        }

                                        $isExternal = $imgPath && strpos($imgPath, 'http') === 0;
                                        $imgSrc = $imgPath ? ($isExternal ? $imgPath : asset('storage/' . ltrim($imgPath, '/'))) : 'https://via.placeholder.com/800x600?text=Hotel+Image';
                                    @endphp

                                    <div class="border rounded-lg p-4">
                                        <div class="h-36 bg-gray-100 rounded-md overflow-hidden">
                                            <img src="{{ $imgSrc }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <h3 class="mt-3 font-semibold">{{ $hotel->name }}</h3>
                                        <p class="text-sm text-black/60">{{ is_array($hotel->address) ? ($hotel->address['city'] ?? '') : (optional($hotel->address)->city ?? '') }} · {{ $hotel->rating }}★</p>
                                        <div class="mt-3 flex items-center justify-between">
                                            <div class="text-lg font-bold">${{ number_format($hotel->base_price,2) }}</div>
                                            <a href="#" class="text-sm text-blue-600">View</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
