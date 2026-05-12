@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Manage ' . ucfirst($type) . 's')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">{{ ucfirst($type) }}s</h1>
            <p class="text-muted small">Manage your hotel {{ $type }}s and promotions.</p>
        </div>
        <a href="{{ route('admin.coupons.create', ['type' => $type]) }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Add New {{ ucfirst($type) }}</span>
        </a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title / Code</th>
                        <th>Discount</th>
                        <th>Validity</th>
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
                                @if($coupon->code)
                                    <span class="badge bg-light text-dark border extra-small">CODE: {{ $coupon->code }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success">
                                    {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value.'%' : \App\Helpers\CurrencyHelper::format($coupon->discount_value) }}
                                </span>
                                <div class="extra-small text-muted">{{ ucfirst($coupon->discount_type) }}</div>
                            </td>
                            <td>
                                <div class="small">
                                    {{ $coupon->start_date ? $coupon->start_date->format('M d, Y') : 'Immediate' }} - 
                                    {{ $coupon->expire_date ? $coupon->expire_date->format('M d, Y') : 'Never' }}
                                </div>
                                @if(!$coupon->isActive())
                                    <span class="badge bg-soft-danger text-danger extra-small">Expired/Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $coupon->status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $coupon->status ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-light btn-sm border">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm border text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No {{ $type }}s found.
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
