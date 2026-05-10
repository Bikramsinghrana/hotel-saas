@extends('layouts.app')

@push('styles')
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
    position: absolute; inset: 0;
    background-image: url('https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1600&h=900&fit=crop&q=90');
    background-size: cover; background-position: center;
    opacity: 0.45;
}
.hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, rgba(15,23,42,0.6) 0%, rgba(15,23,42,0.85) 100%);
}
.hero-content {
    position: relative; z-index: 2;
    text-align: center;
    padding: 2rem 1.5rem 4rem;
    max-width: 860px;
}
.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 6vw, 4rem);
    font-weight: 700; color: #fff; line-height: 1.15;
    margin-bottom: 1.25rem;
}
.hero-title span { color: #4ade80; }
.hero-subtitle {
    font-size: 1.15rem; color: #cbd5e1; max-width: 560px;
    margin: 0 auto; line-height: 1.7;
}

/* ── SEARCH BAR ── */
.search-wrap {
    position: relative; z-index: 10;
    max-width: 1000px; margin: -2.5rem auto 0;
    padding: 0 1.5rem;
}
.search-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    padding: 1.5rem;
    display: grid; grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem; align-items: end;
}
@media(max-width:768px){ .search-card { grid-template-columns: 1fr 1fr; } }
@media(max-width:520px){ .search-card { grid-template-columns: 1fr; } }
.search-field label {
    display: block; font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 0.4rem;
}
.search-field input, .search-field select {
    width: 100%; padding: 0.7rem 0.85rem; border: 1.5px solid #e2e8f0;
    border-radius: 10px; font-size: 0.9rem; font-family: inherit;
    color: #1e293b; background: #f8fafc; outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.search-field input:focus, .search-field select:focus {
    border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
    background: #fff;
}
.btn-search {
    padding: 0.75rem 1.75rem; background: #16a34a; color: #fff;
    border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(22,163,74,0.35);
}
.btn-search:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(22,163,74,.45); }

/* ── SECTION ── */
.section { padding: 4rem 1.5rem 3rem; max-width: 1280px; margin: 0 auto; }
.section-head { text-align: center; margin-bottom: 3rem; }
.section-kicker {
    display: inline-block; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; color: #16a34a; margin-bottom: 0.6rem;
}
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 700;
    color: #0f172a; margin-bottom: 0.75rem;
}

/* ── ROOM CARDS ── */
.rooms-grid { display: grid; gap: 1.75rem; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }

.room-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #f1f5f9; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: transform .3s, box-shadow .3s;
    display: flex; flex-direction: column;
}
.room-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.13); }

.room-img-wrap { position: relative; height: 240px; overflow: hidden; background: #e2e8f0; }
.room-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s; }
.room-card:hover .room-img-wrap img { transform: scale(1.07); }

.room-tag {
    position: absolute; top: 12px; left: 12px;
    background: #16a34a; color: #fff;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
    padding: 0.3rem 0.7rem; border-radius: 6px;
}

.room-body { padding: 1.25rem 1.4rem 1.4rem; flex-grow: 1; display: flex; flex-direction: column; }
.room-name { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem; line-height: 1.3; }
.room-info { display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.85rem; color: #64748b; margin-bottom: 1rem; }
.room-info span { display: flex; align-items: center; gap: 0.3rem; }

.room-facilities { margin-bottom: 1.25rem; display: flex; flex-wrap: wrap; gap: 0.4rem; flex-grow: 1; align-content: flex-start; }
.facility-badge {
    background: #f1f5f9; color: #475569; font-size: 0.75rem;
    padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 600;
}

.room-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid #f1f5f9; }
.room-price-lbl { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
.room-price-num { font-size: 1.5rem; font-weight: 800; color: #0f172a; }
.room-price-per { font-size: 0.78rem; color: #94a3b8; font-weight: 400; }
.btn-book {
    padding: 0.55rem 1.25rem; background: #0f172a; color: #fff;
    text-decoration: none; border-radius: 8px; font-size: 0.85rem;
    font-weight: 600; transition: background .2s, transform .15s;
}
.btn-book:hover { background: #16a34a; transform: translateY(-1px); color: #fff; }

.demo-banner {
    background: #fefce8; border: 1px solid #fde047; border-radius: 12px;
    padding: 1rem 1.25rem; margin-bottom: 2rem;
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.875rem; color: #854d0e;
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
            Find the perfect space for your stay. Whether you're here for business or leisure, our luxurious accommodations guarantee a memorable experience.
        </p>
    </div>
</section>

{{-- ── FILTER / SEARCH BAR ── --}}
<x-room-filter />

{{-- ── ROOM CARDS ── --}}
<div class="section" id="rooms">
    @if($useDummy ?? false)
        <div class="demo-banner">
            ⚠️ <strong>Demo Mode:</strong>&nbsp; Showing sample room data. Create rooms in the admin panel to display real data.
        </div>
    @endif

    <div class="rooms-grid">
        @forelse($rooms as $room)
            @php
                $imgPath = null;
                try {
                    if (is_array($room->gallery) && isset($room->gallery['image_path'])) {
                        $imgPath = $room->gallery['image_path'];
                    } elseif (is_string($room->gallery ?? null)) {
                        $imgPath = $room->gallery;
                    } elseif (!empty($room->gallery) && is_iterable($room->gallery)) {
                        $first = is_array($room->gallery) ? reset($room->gallery) : (method_exists($room->gallery,'first') ? $room->gallery->first() : null);
                        $imgPath = is_object($first) ? ($first->path ?? null) : ($first['path'] ?? null);
                    }
                } catch (\Throwable $e) { $imgPath = null; }

                $imgSrc = $imgPath
                    ? (str_starts_with($imgPath,'http') ? $imgPath : asset('storage/'.ltrim($imgPath,'/')))
                    : 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&h=600&fit=crop';
            @endphp

            <div class="room-card">
                <div class="room-img-wrap">
                    <img src="{{ $imgSrc }}" alt="{{ $room->room_type }}" loading="lazy">
                    @if($room->status == 'active')
                        <span class="room-tag">Available</span>
                    @endif
                </div>
                <div class="room-body">
                    <h3 class="room-name">{{ $room->room_type }}</h3>
                    
                    <div class="room-info">
                        <span><i class="fas fa-user text-muted"></i> Up to {{ $room->max_adults }} Adults</span>
                        @if($room->max_children > 0)
                            <span><i class="fas fa-child text-muted"></i> {{ $room->max_children }} Children</span>
                        @endif
                        <span><i class="fas fa-bed text-muted"></i> {{ $room->total_rooms }} Rooms Left</span>
                    </div>

                    @if(!empty($room->facilities) && is_array($room->facilities))
                        <div class="room-facilities">
                            @foreach(array_slice($room->facilities, 0, 4) as $facility)
                                <span class="facility-badge">{{ $facility }}</span>
                            @endforeach
                            @if(count($room->facilities) > 4)
                                <span class="facility-badge">+{{ count($room->facilities) - 4 }} more</span>
                            @endif
                        </div>
                    @else
                        <div class="room-facilities">
                            <span class="facility-badge">Comfort</span>
                            <span class="facility-badge">Luxury</span>
                        </div>
                    @endif

                    <div class="room-footer">
                        <div>
                            <div class="room-price-lbl">Starting from</div>
                            <div class="room-price-num">${{ number_format($room->price_per_day, 0) }}<span class="room-price-per"> / Night</span></div>
                        </div>
                        <a href="{{ route('rooms.checkout', array_merge(request()->all(), ['id' => $room->id])) }}" class="btn-book">Book Now</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <h4>No rooms found matching your criteria.</h4>
                <p>Try adjusting your search filters.</p>
                <a href="{{ route('rooms.index') }}" class="btn-search mt-3 d-inline-block" style="text-decoration: none;">Clear Filters</a>
            </div>
        @endforelse
    </div>

    @if(method_exists($rooms, 'links') && $rooms->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $rooms->withQueryString()->links() }}
        </div>
    @endif
</div>

@endsection
