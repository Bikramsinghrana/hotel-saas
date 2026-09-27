@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Manage ' . ucfirst($type) . 's')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">{{ ucfirst($type) }}s & Promotions</h1>
            <p class="text-muted small">Manage your hotel discounts, days-wise offers, and time-slot promo codes.</p>
        </div>
        <a href="{{ route('admin.coupons.create', ['type' => $type]) }}" class="btn btn-primary d-flex align-items-center gap-2 fw-semibold">
            <i class="fas fa-plus"></i>
            <span>Add New {{ ucfirst($type) }}</span>
        </a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">Image</th>
                        <th>Title / Code</th>
                        <th>Discount</th>
                        <th>Schedule & Days</th>
                        <th>Time Window</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <img src="{{ $coupon->image ? asset('storage/'.$coupon->image) : 'https://placehold.co/60x40?text=No+Img' }}" 
                                     class="rounded border" style="width: 60px; height: 40px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $coupon->title }}</div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    @if($coupon->code)
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.725rem;">CODE: {{ $coupon->code }}</span>
                                    @endif
                                    @if($coupon->hotel)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">{{ $coupon->hotel->name }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">All Properties</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success" style="font-size: 1.05rem;">
                                    {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value.'%' : \App\Helpers\CurrencyHelper::format($coupon->discount_value) }}
                                </span>
                                <div class="extra-small text-muted">{{ ucfirst($coupon->discount_type) }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    {{ $coupon->start_date ? $coupon->start_date->format('d M Y') : 'Immediate' }} - 
                                    {{ $coupon->expire_date ? $coupon->expire_date->format('d M Y') : 'Never' }}
                                </div>
                                <div class="mt-1">
                                    @if(empty($coupon->applicable_days) || count($coupon->applicable_days) === 7)
                                        <span class="badge bg-primary-subtle text-primary extra-small">All 7 Days</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small" title="{{ implode(', ', $coupon->applicable_days) }}">
                                            <i class="fas fa-calendar-day me-1"></i> {{ count($coupon->applicable_days) }} Days ({{ implode(', ', array_map(fn($d) => ucfirst(substr($d,0,3)), $coupon->applicable_days)) }})
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(!empty($coupon->start_time) && !empty($coupon->end_time))
                                    <div class="small fw-semibold text-dark">
                                        <i class="fas fa-clock text-primary me-1"></i> {{ $coupon->time_formatted }}
                                    </div>
                                    <span class="badge bg-success-subtle text-success extra-small">Time Restricted</span>
                                @else
                                    <span class="badge bg-light text-muted border extra-small">24 Hours (All Day)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $coupon->status && $coupon->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $coupon->status && $coupon->isActive() ? 'Active' : ($coupon->status ? 'Restricted' : 'Disabled') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-light btn-sm border" title="Edit Coupon">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm border text-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-ticket-alt fa-2x mb-3 text-muted opacity-50 d-block"></i>
                                No {{ $type }}s found. Click "Add New {{ ucfirst($type) }}" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
