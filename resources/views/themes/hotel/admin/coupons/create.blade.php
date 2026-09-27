@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Add New ' . ucfirst($type))

@push('styles')
<style>
.day-pill-checkbox input[type="checkbox"] {
    display: none;
}
.day-pill-checkbox label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0.85rem;
    border-radius: 0.5rem;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    min-width: 54px;
}
.day-pill-checkbox input[type="checkbox"]:checked + label {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
}
.quick-day-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.15s ease;
}
.quick-day-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.validity-rule-box {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.rule-badge-pill {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
    border-radius: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">Add New {{ ucfirst($type) }}</h1>
            <p class="text-muted small">Create a new scheduled promotion, day-wise coupon or special discount.</p>
        </div>
        <a href="{{ route('admin.coupons.index', ['type' => $type]) }}" class="btn btn-light border d-flex align-items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>

    <form action="{{ route('admin.coupons.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        
        <div class="row g-4">
            <!-- Main Content Left Column -->
            <div class="col-lg-8">
                <!-- Basic Info Card -->
                <div class="admin-card mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-tag text-primary me-2"></i>Basic Details</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Weekend Lunch Special 20% Off" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Coupon Code (Optional for Offers)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-barcode text-muted"></i></span>
                                <input type="text" name="code" class="form-control text-uppercase font-monospace @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="LUNCH20">
                            </div>
                            @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Target Property (Optional)</label>
                            <select name="hotel_id" class="form-select form-select-sm @error('hotel_id') is-invalid @enderror">
                                <option value="">All Hotels / Branches</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                            @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Discount Type <span class="text-danger">*</span></label>
                            <select name="discount_type" class="form-select form-select-sm @error('discount_type') is-invalid @enderror">
                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                            </select>
                            @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Discount Value <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" class="form-control form-control-sm @error('discount_value') is-invalid @enderror" step="0.01" value="{{ old('discount_value', 0) }}" required>
                            @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2" placeholder="Explain the terms or benefits of this coupon/offer...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Validity & Restriction Mode Selector Card -->
                <div class="admin-card mb-4 border-top border-3 border-primary shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sliders-h me-2"></i>Coupon Validity Rule</h5>
                            <p class="text-muted small mb-0">Select whether coupon is valid on specific days, dates, time windows, or anytime.</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark"><i class="fas fa-hand-pointer text-primary me-1"></i> Choose Restriction Mode</label>
                        <select name="validity_type" id="validityTypeSelect" class="form-select form-select-md fw-bold border-primary" onchange="handleValidityTypeChange(this.value)">
                            <option value="all" {{ old('validity_type', 'all') == 'all' ? 'selected' : '' }}>🌟 Always Valid (24/7 - No Date, Day or Time restriction)</option>
                            <option value="date" {{ old('validity_type') == 'date' ? 'selected' : '' }}>📅 Date Range Wise (Valid between Start & Expiry dates)</option>
                            <option value="days" {{ old('validity_type') == 'days' ? 'selected' : '' }}>📆 Days Wise (Valid only on selected days: Sun, Mon, etc.)</option>
                            <option value="time" {{ old('validity_type') == 'time' ? 'selected' : '' }}>⏰ Time Window Wise / Happy Hours (e.g. 12:30 PM - 03:30 PM)</option>
                            <option value="days_time" {{ old('validity_type') == 'days_time' ? 'selected' : '' }}>⚡ Days + Time Window Combined (e.g. Weekends 12:30 to 03:30)</option>
                            <option value="custom" {{ old('validity_type') == 'custom' ? 'selected' : '' }}>🎯 Custom / All Restrictions Combined</option>
                        </select>
                    </div>

                    <!-- Box 1: Always Active Info Box -->
                    <div id="boxAlwaysActive" class="validity-rule-box p-3 rounded-3 bg-light border border-info-subtle mb-0" style="display: none;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-check-circle text-success fs-3"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Unrestricted 24/7 Validity</h6>
                                <p class="text-muted small mb-0">This coupon is always active and will apply across all dates, all days of the week, and anytime 24 hours a day.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Date Range Box -->
                    <div id="boxDateRange" class="validity-rule-box mt-3 p-3 bg-light rounded-3 border border-primary-subtle" style="display: none;">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Date Range Validity</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Start Date</label>
                                <input type="date" name="start_date" id="startDateInput" class="form-control form-control-sm @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Expiry Date</label>
                                <input type="date" name="expire_date" id="expireDateInput" class="form-control form-control-sm @error('expire_date') is-invalid @enderror" value="{{ old('expire_date') }}">
                                @error('expire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Days of Week Box -->
                    <div id="boxDaysSchedule" class="validity-rule-box mt-3 p-3 bg-light rounded-3 border border-primary-subtle" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary mb-0"><i class="fas fa-calendar-week me-2"></i>Select Applicable Days of Week</h6>
                            <div class="d-flex gap-1">
                                <button type="button" class="quick-day-btn" onclick="selectDays('all')">All Days</button>
                                <button type="button" class="quick-day-btn" onclick="selectDays('weekdays')">Weekdays</button>
                                <button type="button" class="quick-day-btn" onclick="selectDays('weekends')">Weekends</button>
                                <button type="button" class="quick-day-btn" onclick="selectDays('none')">Clear</button>
                            </div>
                        </div>

                        <p class="text-muted small mb-3">Click on the days below when this coupon/offer should be active:</p>

                        @php
                            $daysList = [
                                'sunday' => 'Sunday',
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday'
                            ];
                            $selectedDays = old('applicable_days', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
                        @endphp

                        <div class="d-flex flex-wrap gap-2 mb-1" id="daysPillContainer">
                            @foreach($daysList as $dayVal => $dayLabel)
                                <div class="day-pill-checkbox">
                                    <input type="checkbox" name="applicable_days[]" value="{{ $dayVal }}" id="day_{{ $dayVal }}" 
                                           class="day-checkbox" 
                                           data-day="{{ $dayVal }}"
                                           {{ in_array($dayVal, (array)$selectedDays) ? 'checked' : '' }}>
                                    <label for="day_{{ $dayVal }}">
                                        <span>{{ $dayLabel }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('applicable_days') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Box 4: Time Window Box -->
                    <div id="boxTimeSchedule" class="validity-rule-box mt-3 p-3 bg-light rounded-3 border border-primary-subtle" style="display: none;">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-clock me-2"></i>Time Window / Happy Hours</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold small text-secondary">Preset Time Slot Dropdown</label>
                                <select name="time_slot" id="timeSlotPreset" class="form-select form-select-sm" onchange="handleTimeSlotPreset(this.value)">
                                    <option value="all_day" {{ old('time_slot') == 'all_day' ? 'selected' : '' }}>All Day (24 Hours - No Time Restriction)</option>
                                    <option value="12:30_15:30" {{ old('time_slot') == '12:30_15:30' ? 'selected' : '' }}>Lunch / Afternoon Window (12:30 PM - 03:30 PM)</option>
                                    <option value="16:00_19:00" {{ old('time_slot') == '16:00_19:00' ? 'selected' : '' }}>Happy Hours (04:00 PM - 07:00 PM)</option>
                                    <option value="19:30_23:00" {{ old('time_slot') == '19:30_23:00' ? 'selected' : '' }}>Dinner Special (07:30 PM - 11:00 PM)</option>
                                    <option value="06:00_10:00" {{ old('time_slot') == '06:00_10:00' ? 'selected' : '' }}>Breakfast Special (06:00 AM - 10:00 AM)</option>
                                    <option value="custom" {{ old('time_slot') == 'custom' ? 'selected' : '' }}>Custom Time Window...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Start Time</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="fas fa-hourglass-start text-muted"></i></span>
                                    <input type="time" name="start_time" id="startTimeInput" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}">
                                </div>
                                @error('start_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">End Time</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="fas fa-hourglass-end text-muted"></i></span>
                                    <input type="time" name="end_time" id="endTimeInput" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}">
                                </div>
                                @error('end_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Right Column -->
            <div class="col-lg-4">
                <!-- Status & Active Switch -->
                <div class="admin-card mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Status</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="status" {{ old('status', 'on') == 'on' ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="status">Active / Enabled</label>
                    </div>
                </div>

                <!-- Spend & Limits Card -->
                <div class="admin-card mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Usage Restrictions</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Minimum Spend Amount (₹)</label>
                        <input type="number" name="min_spend" class="form-control form-control-sm" step="0.01" value="{{ old('min_spend', 0) }}" placeholder="0 for no minimum">
                        <small class="text-muted extra-small">Minimum order/room total required.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Total Usage Limit</label>
                        <input type="number" name="usage_limit" class="form-control form-control-sm" min="1" value="{{ old('usage_limit') }}" placeholder="Unlimited if empty">
                        <small class="text-muted extra-small">Max total times coupon can be redeemed.</small>
                    </div>
                </div>

                <!-- Banner Image Card -->
                <div class="admin-card mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Banner Image</h6>
                    <div class="mb-3">
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <p class="text-muted extra-small mt-2">Recommended size: 800x600 for promo cards.</p>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> Save {{ ucfirst($type) }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function handleValidityTypeChange(val) {
    const boxAlways = document.getElementById('boxAlwaysActive');
    const boxDate = document.getElementById('boxDateRange');
    const boxDays = document.getElementById('boxDaysSchedule');
    const boxTime = document.getElementById('boxTimeSchedule');

    if (val === 'all') {
        if(boxAlways) boxAlways.style.display = 'block';
        if(boxDate) boxDate.style.display = 'none';
        if(boxDays) boxDays.style.display = 'none';
        if(boxTime) boxTime.style.display = 'none';
    } else if (val === 'date') {
        if(boxAlways) boxAlways.style.display = 'none';
        if(boxDate) boxDate.style.display = 'block';
        if(boxDays) boxDays.style.display = 'none';
        if(boxTime) boxTime.style.display = 'none';
    } else if (val === 'days') {
        if(boxAlways) boxAlways.style.display = 'none';
        if(boxDate) boxDate.style.display = 'none';
        if(boxDays) boxDays.style.display = 'block';
        if(boxTime) boxTime.style.display = 'none';
    } else if (val === 'time') {
        if(boxAlways) boxAlways.style.display = 'none';
        if(boxDate) boxDate.style.display = 'none';
        if(boxDays) boxDays.style.display = 'none';
        if(boxTime) boxTime.style.display = 'block';
    } else if (val === 'days_time') {
        if(boxAlways) boxAlways.style.display = 'none';
        if(boxDate) boxDate.style.display = 'none';
        if(boxDays) boxDays.style.display = 'block';
        if(boxTime) boxTime.style.display = 'block';
    } else if (val === 'custom') {
        if(boxAlways) boxAlways.style.display = 'none';
        if(boxDate) boxDate.style.display = 'block';
        if(boxDays) boxDays.style.display = 'block';
        if(boxTime) boxTime.style.display = 'block';
    }
}

function selectDays(mode) {
    const checkboxes = document.querySelectorAll('.day-checkbox');
    checkboxes.forEach(cb => {
        const day = cb.dataset.day;
        if (mode === 'all') {
            cb.checked = true;
        } else if (mode === 'none') {
            cb.checked = false;
        } else if (mode === 'weekdays') {
            cb.checked = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].includes(day);
        } else if (mode === 'weekends') {
            cb.checked = ['saturday', 'sunday'].includes(day);
        }
    });
}

function handleTimeSlotPreset(val) {
    const startInput = document.getElementById('startTimeInput');
    const endInput = document.getElementById('endTimeInput');

    if (val === 'all_day') {
        startInput.value = '';
        endInput.value = '';
    } else if (val === '12:30_15:30') {
        startInput.value = '12:30';
        endInput.value = '15:30';
    } else if (val === '16:00_19:00') {
        startInput.value = '16:00';
        endInput.value = '19:00';
    } else if (val === '19:30_23:00') {
        startInput.value = '19:30';
        endInput.value = '23:00';
    } else if (val === '06:00_10:00') {
        startInput.value = '06:00';
        endInput.value = '10:00';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('validityTypeSelect');
    if (select) {
        handleValidityTypeChange(select.value);
    }
});
</script>
@endpush
