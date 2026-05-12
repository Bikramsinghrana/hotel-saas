@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Edit ' . ucfirst($type))

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">Edit {{ ucfirst($type) }}</h1>
            <p class="text-muted small">Update your hotel {{ $type }} details.</p>
        </div>
        <a href="{{ route('admin.coupons.index', ['type' => $type]) }}" class="btn btn-light border d-flex align-items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" value="{{ old('title', $coupon->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Coupon Code (Optional for Offers)</label>
                            <input type="text" name="code" class="form-control form-control-sm @error('code') is-invalid @enderror" value="{{ old('code', $coupon->code) }}">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Target Hotel (Optional)</label>
                            <select name="hotel_id" class="form-select form-select-sm @error('hotel_id') is-invalid @enderror">
                                <option value="">All Hotels</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ old('hotel_id', $coupon->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                            @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Discount Type</label>
                            <select name="discount_type" class="form-select form-select-sm @error('discount_type') is-invalid @enderror">
                                <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                            @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Discount Value</label>
                            <input type="number" name="discount_value" class="form-control form-control-sm @error('discount_value') is-invalid @enderror" step="0.01" value="{{ old('discount_value', $coupon->discount_value) }}" required>
                            @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="4">{{ old('description', $coupon->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Status & Schedule</h6>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="status" {{ old('status', $coupon->status ? 'on' : 'off') == 'on' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="status">Enabled</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm @error('start_date') is-invalid @enderror" value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '') }}">
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Expiry Date</label>
                        <input type="date" name="expire_date" class="form-control form-control-sm @error('expire_date') is-invalid @enderror" value="{{ old('expire_date', $coupon->expire_date ? $coupon->expire_date->format('Y-m-d') : '') }}">
                        @error('expire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="admin-card">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Banner Image</h6>
                    @if($coupon->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/'.$coupon->image) }}" class="w-100 rounded border mb-2 shadow-sm">
                        </div>
                    @endif
                    <div class="mb-3">
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <p class="text-muted extra-small mt-2">Upload a new image to replace current one.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Update {{ ucfirst($type) }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
