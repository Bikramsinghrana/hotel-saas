@extends('themes.hotel.layouts.admin')

@section('title', 'Room Wizard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    [x-cloak] { display: none !important; }
    .wizard-step-active { color: var(--primary); border-bottom: 2px solid var(--primary); }
    .dropzone { border: 2px dashed #e2e8f0; border-radius: 12px; background: #f8fafc; }
</style>
@endpush

@section('content')
<div class="container-fluid p-0" x-data='roomWizard(@json($room ?? []), @json($step ?? 1))' x-cloak>
    <div class="mb-4">
        <h1 class="page-title">{{ $room && $room->id ? 'Edit Room' : 'Create New Room' }}</h1>
    </div>

    <!-- Wizard Progress -->
    <div class="admin-card p-0 mb-4 overflow-hidden">
        <div class="d-flex border-bottom bg-light">
            <template x-for="i in 4">
                <div class="flex-fill p-3 text-center cursor-pointer transition" 
                     :class="currentStep == i ? 'wizard-step-active bg-white fw-bold' : (currentStep > i ? 'text-success' : 'text-muted')"
                     @click="goToStep(i)">
                    <span class="d-block small text-uppercase fw-bold" x-text="'Step ' + i"></span>
                    <span class="d-none d-md-inline" x-text="getStepTitle(i)"></span>
                    <i class="fas fa-check-circle ms-1" x-show="currentStep > i"></i>
                </div>
            </template>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Step 1: General Details -->
            <div x-show="currentStep == 1" class="admin-card">
                <h5 class="fw-bold mb-4">Step 1: General Information</h5>
                <form @submit.prevent="submitStep(1)">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Room Name</label>
                            <input type="text" x-model="formData.post_title" class="form-control" placeholder="e.g. Deluxe Ocean View" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Room Slug</label>
                            <input type="text" x-model="formData.room_slug" class="form-control" placeholder="deluxe-ocean-view" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Room Type</label>
                            <select x-model="formData.room_type_id" class="form-select" required>
                                <option value="">-- Choose Room Type --</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->room_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select x-model="formData.status" class="form-select" required>
                                <option value="draft">Draft</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea x-model="formData.post_content" class="form-control" rows="4" placeholder="Describe the room features..."></textarea>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" :disabled="loading">
                            <span x-show="!loading">Save & Next</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Pricing & Capacity -->
            <div x-show="currentStep == 2" class="admin-card">
                <h5 class="fw-bold mb-4">Step 2: Pricing & Capacity</h5>
                <form @submit.prevent="submitStep(2)">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Total Rooms</label>
                            <input type="number" x-model="formData.total_rooms" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Max Days</label>
                            <input type="number" x-model="formData.day" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Adult Capacity</label>
                            <input type="number" x-model="formData.max_adults" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Child Capacity</label>
                            <input type="number" x-model="formData.max_children" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Basic Price ($)</label>
                            <input type="number" x-model="formData.base_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Member Price ($)</label>
                            <input type="number" x-model="formData.member_price" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Discount (%)</label>
                            <input type="number" x-model="formData.discount" class="form-control" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tax (%)</label>
                            <input type="number" x-model="formData.tax" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Coupon Code</label>
                            <input type="text" x-model="formData.coupon" class="form-control" placeholder="SUMMER25">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Default Check-in Date</label>
                            <input type="date" x-model="formData.check_in" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Default Check-out Date</label>
                            <input type="date" x-model="formData.check_out" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" @click="currentStep = 1" class="btn btn-light px-4">Back</button>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" :disabled="loading">
                            <span x-show="!loading">Save & Next</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Services, Facilities & Media -->
            <div x-show="currentStep == 3">
                <div class="admin-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Step 3: Services & Facilities</h5>
                    </div>

                    <div class="row mb-4">
                        <!-- Facilities Section -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-bold">Room Facilities</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalAddFacility">
                                    <i class="fas fa-plus-circle"></i> Add Facility
                                </button>
                            </div>
                            <div class="p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto;">
                                @foreach($facilities as $facility)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="{{ $facility->id }}" 
                                               x-model="formData.facilities" id="fac_{{ $facility->id }}">
                                        <label class="form-check-label" for="fac_{{ $facility->id }}">
                                            {{ $facility->title }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Extra Services Section -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-bold">Extra Services</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalAddService">
                                    <i class="fas fa-plus-circle"></i> Add Service
                                </button>
                            </div>
                            <div class="p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto;">
                                @foreach($extraServices as $service)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="{{ $service->id }}" 
                                               x-model="formData.extra_services" id="serv_{{ $service->id }}">
                                        <label class="form-check-label" for="serv_{{ $service->id }}">
                                            {{ $service->title }} ({{ $service->price_type }}{{ $service->price }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Room Gallery</label>
                        <div id="dropzone-room-media" class="dropzone"></div>
                    </div>

                    <div class="row g-3">
                        <template x-for="m in uploadedMedia" :key="m.id">
                            <div class="col-md-3">
                                <div class="position-relative">
                                    <img :src="'/storage/' + m.path" class="img-fluid rounded border shadow-sm" style="height: 100px; width: 100%; object-fit: cover;">
                                    <button @click="deleteMedia(m.id)" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-1" style="font-size: 0.6rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <form @submit.prevent="submitStep(3)" class="mt-4">
                        <div class="d-flex justify-content-between">
                            <button type="button" @click="currentStep = 2" class="btn btn-light px-4">Back</button>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" :disabled="loading">
                                <span x-show="!loading">Save & Next</span>
                                <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Step 4: Review -->
            <div x-show="currentStep == 4" class="admin-card">
                <h5 class="fw-bold mb-4">Step 4: Review & Confirm</h5>
                <form @submit.prevent="submitStep(4)">
                    <div class="mb-3 bg-light p-4 rounded border">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">General Information</h6>
                                <p class="mb-1"><strong>Name:</strong> <span x-text="formData.post_title"></span></p>
                                <p class="mb-1"><strong>Slug:</strong> <span x-text="formData.room_slug"></span></p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-primary text-capitalize" x-text="formData.status"></span></p>
                                <p class="mb-1"><strong>Coupon:</strong> <span x-text="formData.coupon || 'None'"></span></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Pricing & Capacity</h6>
                                <p class="mb-1"><strong>Basic Price:</strong> $<span x-text="formData.base_price"></span></p>
                                <p class="mb-1"><strong>Discount:</strong> <span x-text="formData.discount"></span>%</p>
                                <p class="mb-1"><strong>Tax:</strong> <span x-text="formData.tax"></span>%</p>
                                <p class="mb-1"><strong>Max Adults:</strong> <span x-text="formData.max_adults"></span></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Schedule</h6>
                                <p class="mb-1"><strong>Check-in:</strong> <span x-text="formData.check_in || 'N/A'"></span></p>
                                <p class="mb-1"><strong>Check-out:</strong> <span x-text="formData.check_out || 'N/A'"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Selections</h6>
                                <p class="mb-1"><strong>Facilities:</strong> <span x-text="formData.facilities.length"></span> selected</p>
                                <p class="mb-1"><strong>Extra Services:</strong> <span x-text="formData.extra_services.length"></span> selected</p>
                                <p class="mb-0"><strong>Images:</strong> <span x-text="uploadedMedia.length"></span> uploaded</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" @click="currentStep = 3" class="btn btn-light px-4">Back</button>
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold" :disabled="loading">
                            <span x-show="!loading">Activate Room</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Activating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="admin-card sticky-top" style="top: 2rem;">
                <h6 class="fw-bold mb-3">Live Preview</h6>
                <div class="border rounded overflow-hidden bg-white shadow-sm">
                    <img :src="uploadedMedia.length > 0 ? '/storage/' + uploadedMedia[0].path : 'https://placehold.co/600x400?text=No+Image'" class="w-100" style="height: 180px; object-fit: cover;">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 text-primary" x-text="formData.post_title || 'Room Name'"></h6>
                            <span class="badge bg-soft-success text-success small" x-text="formData.status"></span>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Price / Night</span>
                                <span class="fw-bold text-dark" x-text="'$' + formData.base_price"></span>
                            </div>
                            <template x-if="formData.discount > 0">
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="text-danger small">Discount</span>
                                    <span class="text-danger small" x-text="'-' + formData.discount + '%'"></span>
                                </div>
                            </template>
                        </div>

                        <div class="pt-2 border-top">
                            <div class="d-flex align-items-center mb-1">
                                <i class="far fa-calendar-alt text-muted me-2" style="font-size: 0.8rem;"></i>
                                <span class="text-muted" style="font-size: 0.75rem;">Listing Date: {{ date('d M, Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center" x-show="formData.coupon">
                                <i class="fas fa-tag text-muted me-2" style="font-size: 0.8rem;"></i>
                                <span class="text-muted" style="font-size: 0.75rem;">Coupon: <span class="fw-bold text-dark" x-text="formData.coupon"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for dynamic creation -->
    <div class="modal fade" id="modalAddFacility" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Facility</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Facility Title</label>
                        <input type="text" x-model="newTerm.title" class="form-control" placeholder="e.g. Free Wi-Fi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description (Optional)</label>
                        <textarea x-model="newTerm.description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" @click="saveTerm('facility')" class="btn btn-primary fw-bold" :disabled="loading">Save Facility</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddService" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Extra Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Service Title</label>
                        <input type="text" x-model="newTerm.title" class="form-control" placeholder="e.g. Airport Pickup">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Price</label>
                            <input type="number" x-model="newTerm.price" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Currency/Type</label>
                            <input type="text" x-model="newTerm.price_type" class="form-control" placeholder="$">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description (Optional)</label>
                        <textarea x-model="newTerm.description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" @click="saveTerm('extra_service')" class="btn btn-primary fw-bold" :disabled="loading">Save Service</button>
                </div>
            </div>
        </div>
    </div>
</div>
        
        <div class="col-lg-4">
            <div class="admin-card">
                <h6 class="fw-bold mb-3">Room Information</h6>
                <div class="border rounded p-3 bg-light">
                    <h5 class="fw-bold text-primary" x-text="formData.room_slug || 'Room Name'"></h5>
                    <p class="text-muted small">This information will be displayed to customers.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('themes.hotel.admin.partials.wizard-scripts')
@endpush
