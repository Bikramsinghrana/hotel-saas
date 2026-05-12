@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('themes/hotel/css/room-booking.css') }}">
    <style>
        /* ── HERO ── */
        .hero {
            position: relative;
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f2027 100%);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1600&h=900&fit=crop&q=90');
            background-size: cover;
            background-position: center;
            opacity: 0.45;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.85) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem 1.5rem 4rem;
            max-width: 860px;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 6vw, 4rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 1.25rem;
        }

        .hero-title span {
            color: #4ade80;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #cbd5e1;
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ── SEARCH BAR ── */
        .search-wrap {
            position: relative;
            z-index: 10;
            max-width: 1000px;
            margin: -2.5rem auto 0;
            padding: 0 1.5rem;
        }

        .search-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            padding: 1.75rem;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1.25rem;
            align-items: end;
            border: 1px solid #f1f5f9;
        }

        @media(max-width:768px) {
            .search-card {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(max-width:520px) {
            .search-card {
                grid-template-columns: 1fr;
            }
        }

        .search-field label {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .search-field input,
        .search-field select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all .2s ease;
        }

        .search-field input:focus,
        .search-field select:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.08);
            background: #fff;
        }

        .btn-search {
            padding: 0.85rem 2rem;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
            transition: all .2s ease;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.4);
        }

        .btn-search:hover {
            background: #15803d;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 163, 74, .5);
        }

        /* ── SECTION ── */
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        /* ── ROOM CARDS ── */
        .rooms-grid {
            display: grid;
            gap: 2rem;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        }

        .room-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            transition: all .4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .room-img-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #f1f5f9;
        }

        .room-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .8s ease;
        }

        .room-card:hover .room-img-wrap img {
            transform: scale(1.1);
        }

        .room-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(22, 163, 74, 0.9);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
        }

        .room-body {
            padding: 1.75rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .room-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
            line-height: 1.2;
            transition: color 0.3s;
        }

        .room-card:hover .room-name {
            color: #16a34a;
        }

        .room-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.25rem;
        }

        .room-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .room-info i {
            color: #16a34a;
            font-size: 1rem;
        }

        .room-facilities {
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            flex-grow: 1;
            align-content: flex-start;
        }

        .facility-badge {
            background: #f0fdf4;
            color: #16a34a;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            border: 1px solid #dcfce7;
        }

        .room-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.25rem;
            border-top: 1px solid #f8fafc;
        }

        .room-price-lbl {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .room-price-num {
            font-size: 1.75rem;
            font-weight: 900;
            color: #0f172a;
        }

        .room-price-per {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .demo-banner {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            color: #854d0e;
        }

        /* ── EMPTY STATE ── */
        .no-rooms-found {
            grid-column: 1 / -1;
            text-align: center;
            padding: 5rem 2rem;
            background: #f8fafc;
            border-radius: 24px;
            border: 2px dashed #e2e8f0;
            margin-top: 1rem;
        }

        .no-rooms-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }

        .no-rooms-found h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: #0f172a;
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .no-rooms-found p {
            color: #64748b;
            max-width: 500px;
            margin: 0 auto 2.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .btn-clear-filters {
            display: inline-flex;
            align-items: center;
            padding: 0.9rem 2.2rem;
            background: #0f172a;
            color: #fff !important;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }

        .btn-clear-filters:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
        }
    </style>
@endpush

@section('content')

    {{-- ── HERO ── --}}
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">
                Our <span>Rooms & Suites</span>
            </h1>
            <p class="hero-subtitle">
                Find the perfect space for your stay. Whether you're here for business or leisure, our luxurious
                accommodations guarantee a memorable experience.
            </p>
        </div>
    </section>

    {{-- ── FILTER / SEARCH BAR ── --}}
    <x-room-filter />

    {{-- ── ROOM CARDS ── --}}
    <div class="section pt-5" id="rooms">
        <div class="mb-5">
            <h2 class="section-title">Available Accommodations</h2>
            <p class="text-muted">Choose the perfect room for your stay and customize with extra services.</p>
        </div>

        <div class="booking-page-wrap">
            {{-- ── ROOM LIST ── --}}
            <div class="rooms-side">
                @if ($useDummy ?? false)
                    <div class="demo-banner">
                        ⚠️ <strong>Demo Mode:</strong>&nbsp; Showing sample room data. Create rooms in the admin panel to
                        display real data.
                    </div>
                @endif

                <div class="rooms-grid">
                    @forelse($rooms as $room)
                        @php
                            $imgPath = null;
                            $gallery = $room->gallery;
                            if (!empty($gallery) && is_iterable($gallery)) {
                                $galleryArray = is_array($gallery)
                                    ? $gallery
                                    : (method_exists($gallery, 'toArray')
                                        ? $gallery->toArray()
                                        : (array) $gallery);
                                $first = count($galleryArray) > 0 ? reset($galleryArray) : null;
                                $imgPath = is_object($first) ? $first->path ?? null : $first['path'] ?? null;
                            }
                            $imgSrc = $imgPath
                                ? asset('storage/' . $imgPath)
                                : 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&h=600&fit=crop';
                        @endphp

                        <div class="room-card">
                            <div class="room-img-wrap">
                                <img src="{{ $imgSrc }}" alt="{{ $room->post_title }}" loading="lazy">
                                <span class="room-tag">Available</span>
                            </div>
                            <div class="room-body">
                                <h3 class="room-name">{{ $room->room_type }}</h3>

                                <div class="room-info">
                                    <span><i class="fas fa-user text-muted"></i> {{ $room->max_adults }} Adults</span>
                                    <span><i class="fas fa-bed text-muted"></i> {{ $room->number_of_bed ?? 1 }} Bed</span>
                                </div>

                                <div class="room-facilities">
                                    @if (!empty($room->facilities) && is_array($room->facilities))
                                        @foreach (array_slice($room->facilities, 0, 4) as $facility)
                                            <span class="facility-badge">{{ $facility }}</span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="room-footer">
                                    <div>
                                        <div class="room-price-lbl">Per Night</div>
                                        <div class="room-price-num">
                                            @php $finalPrice = $room->price_per_day - ($room->price_per_day * ($room->discount / 100)); @endphp
                                            {{ \App\Helpers\CurrencyHelper::format($finalPrice) }}
                                        </div>
                                    </div>
                                    <div class="price-original small text-muted text-decoration-line-through">
                                        @if ($room->discount > 0)
                                            {{ \App\Helpers\CurrencyHelper::format($room->price_per_day) }}
                                        @endif
                                    </div>
                                </div>

                                {{-- ── BOOKING CONTROLS ── --}}
                                <div class="room-booking-controls">
                                    <div class="extra-services-list">
                                        @foreach ($extraServices as $service)
                                            <label class="extra-service-item">
                                                <input type="checkbox" class="extra-service-chk" value="{{ $service->id }}"
                                                    data-room-id="{{ $room->id }}" data-name="{{ $service->title }}"
                                                    data-price="{{ $service->price }}"
                                                    onchange="handleServiceChange(this)">
                                                <span>{{ $service->title }}
                                                    (+{{ \App\Helpers\CurrencyHelper::format($service->price) }})</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="room-qty-box">
                                        <label>Rooms</label>
                                        <select class="room-qty-select" data-id="{{ $room->id }}"
                                            data-name="{{ $room->post_title }}" data-price="{{ $room->price_per_day }}"
                                            data-discount="{{ $room->discount }}" onchange="handleQtyChange(this)">
                                            <option value="0">0</option>
                                            @for ($i = 1; $i <= min(10, $room->total_rooms); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="no-rooms-found">
                            <div class="no-rooms-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>No Rooms Available</h3>
                            <p>We couldn't find any rooms matching your search criteria. Try adjusting your dates or filters to find more options.</p>
                            <a href="{{ route('rooms.index') }}" class="btn-clear-filters">
                                <i class="fas fa-redo me-2"></i> Clear All Filters
                            </a>
                        </div>
                    @endforelse
                </div>

                @if (method_exists($rooms, 'links') && $rooms->hasPages())
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $rooms->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- ── BOOKING SUMMARY SIDEBAR ── --}}
            <div id="booking-form-section" class="booking-sidebar d-none">
                <div class="booking-summary-card">
                    <h5 class="summary-title">Booking Summary</h5>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Have a coupon?</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="coupon-code" class="form-control" placeholder="CODE">
                            <button class="btn btn-dark" onclick="applyCoupon()">Apply</button>
                        </div>
                        <div id="coupon-message" class="mt-1 small"></div>
                    </div>

                    <div id="booking-summary">
                        {{-- Injected by JS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/common/js/booking.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            BookingSystem.initBooking({
                currencySymbol: '{{ \App\Helpers\CurrencyHelper::format(0, null)[0] }}'
            });

            const checkIn = '{{ request('check_in', now()->format('Y-m-d')) }}';
            const checkOut = '{{ request('check_out', now()->addDay()->format('Y-m-d')) }}';
            const nights = Math.max(1, (new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
            BookingSystem.state.nights = nights;
        });

        function handleQtyChange(el) {
            const d = el.dataset;
            BookingSystem.updateRoomSelection(d.id, d.name, d.price, d.discount, el.value);
        }

        function handleServiceChange(el) {
            const d = el.dataset;
            BookingSystem.updateExtraServices(d.roomId, el.value, d.name, d.price, el.checked);
        }

        async function applyCoupon() {
            const code = document.getElementById('coupon-code').value;
            const msgEl = document.getElementById('coupon-message');
            const res = await BookingSystem.applyCoupon(code);
            msgEl.innerHTML = `<span class="${res.success ? 'text-success' : 'text-danger'}">${res.message}</span>`;
        }
    </script>
@endpush
