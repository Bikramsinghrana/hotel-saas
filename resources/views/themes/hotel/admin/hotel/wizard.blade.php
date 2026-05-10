@extends('themes.hotel.layouts.admin')
{{-- @extends(\App\Helpers\HotelPath::view('layouts.admin')) --}}

@section('title', $hotel ? 'Edit Hotel Wizard' : 'Create Hotel Wizard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    [x-cloak] { display: none !important; }
    .wizard-step-active { color: var(--primary); border-bottom: 2px solid var(--primary); }
    .dropzone { border: 2px dashed #e2e8f0; border-radius: 12px; background: #f8fafc; }
</style>
@endpush

@section('content')
<div class="container-fluid p-0" x-data='hotelWizard(@json($hotel ?? []), @json($step ?? 1))' x-cloak>
    <div class="mb-4">
        <h1 class="page-title">{{ $hotel ? 'Edit Hotel: ' . $hotel->name : 'Create New Hotel' }}</h1>
    </div>

    <!-- Wizard Progress -->
    <div class="admin-card p-0 mb-4 overflow-hidden">
        <div class="d-flex border-bottom bg-light">
            <template x-for="i in 5">
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
            <!-- Step 1: General Info -->
            <div x-show="currentStep == 1" class="admin-card">
                <h5 class="fw-bold mb-4">Step 1: General Information</h5>
                <form @submit.prevent="submitStep(1)">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hotel Name</label>
                        <input type="text" x-model="formData.name" class="form-control" placeholder="Enter hotel name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea x-model="formData.description" class="form-control" rows="4" placeholder="Brief description"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Rating</label>
                            <select x-model="formData.rating" class="form-select">
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select x-model="formData.status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" :disabled="loading">
                            <span x-show="!loading">Save & Next</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Address + Nearby -->
            <div x-show="currentStep == 2" class="admin-card">
                <h5 class="fw-bold mb-4">Step 2: Address & Nearby Places</h5>
                <form @submit.prevent="submitStep(2)">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Address Line</label>
                        <input type="text" x-model="formData.address_line" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" x-model="formData.city" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">State</label>
                            <input type="text" x-model="formData.state" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Country</label>
                            <input type="text" x-model="formData.country" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pincode</label>
                            <input type="text" x-model="formData.pincode" class="form-control" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-bold mb-0">Nearby Places</label>
                            <button type="button" @click="addNearbyPlace" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Add More
                            </button>
                        </div>
                        <template x-for="(place, index) in formData.nearby_places" :key="index">
                            <div class="input-group mb-2">
                                <input type="text" x-model="formData.nearby_places[index]" class="form-control" placeholder="Place name">
                                <button type="button" @click="removeNearbyPlace(index)" class="btn btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
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

            <!-- Step 3: Pricing + Facilities -->
            <div x-show="currentStep == 3" class="admin-card">
                <h5 class="fw-bold mb-4">Step 3: Pricing & Facilities</h5>
                <form @submit.prevent="submitStep(3)">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Price per Night</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" x-model="formData.price_per_night" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Discount (%)</label>
                            <input type="number" x-model="formData.discount_percentage" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tax (%)</label>
                            <input type="number" x-model="formData.tax_percentage" class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-bold mb-0">Facilities</label>
                            <button type="button" @click="addFacility" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Custom
                            </button>
                        </div>
                        <div class="row mb-3">
                            <template x-for="(facility, index) in formData.facilities" :key="index">
                                <div class="col-md-6 mb-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fas fa-check"></i></span>
                                        <input type="text" x-model="formData.facilities[index]" class="form-control">
                                        <button type="button" @click="removeFacility(index)" class="btn btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" @click="currentStep = 2" class="btn btn-light px-4">Back</button>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" :disabled="loading">
                            <span x-show="!loading">Save & Next</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 4: Media -->
            <div x-show="currentStep == 4" class="admin-card">
                <h5 class="fw-bold mb-4">Step 4: Media Upload</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Upload Media</label>
                    <div id="dropzone-media" class="dropzone"></div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Uploaded Media</h6>
                    <div class="row g-3">
                        <template x-for="m in uploadedMedia" :key="m.id">
                            <div class="col-md-3">
                                <div class="position-relative">
                                    {{-- <img src="{{ imageUrl($hotel->image) }}"> --}}
                                    <img :src="'/storage/' + m.path" class="img-fluid rounded border shadow-sm" style="height: 120px; width: 100%; object-fit: cover;">
                                    <span class="badge bg-dark position-absolute top-0 start-0 m-1" x-text="m.type"></span>
                                    <button @click="deleteMedia(m.id)" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="uploadedMedia.length == 0">
                            <div class="col-12 text-center text-muted py-4">
                                No media uploaded yet.
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" @click="currentStep = 3" class="btn btn-light px-4">Back</button>
                    <button type="button" @click="currentStep = 5" class="btn btn-primary px-5 py-2 fw-bold">Next</button>
                </div>
            </div>

            <!-- Step 5: Policies -->
            <div x-show="currentStep == 5" class="admin-card">
                <h5 class="fw-bold mb-4">Step 5: Policies & Completion</h5>
                <form @submit.prevent="submitStep(5)">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cancellation Type</label>
                        <select x-model="formData.cancellation_type" class="form-select">
                            <option value="free">Free Cancellation</option>
                            <option value="paid">Paid Cancellation</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cancel Before (Days)</label>
                        <input type="number" x-model="formData.cancel_before_days" class="form-control" min="0">
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Room Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room Type</label>
                            <input type="text" x-model="roomData.room_type" class="form-control" placeholder="e.g. Deluxe Suite">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price per Day</label>
                            <input type="number" x-model="roomData.price_per_day" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Rooms</label>
                            <input type="number" x-model="roomData.total_rooms" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Adults</label>
                            <input type="number" x-model="roomData.max_adults" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Children</label>
                            <input type="number" x-model="roomData.max_children" class="form-control">
                        </div>
                    </div>
                    <button type="button" @click="saveRoom" class="btn btn-outline-primary btn-sm mb-4">
                        <i class="fas fa-plus me-1"></i> Add Room to Hotel
                    </button>

                    <div class="table-responsive" x-show="rooms.length > 0">
                        <table class="table table-sm small border">
                            <thead class="bg-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(room, index) in rooms" :key="index">
                                    <tr>
                                        <td x-text="room.room_type"></td>
                                        <td x-text="'$' + room.price_per_day"></td>
                                        <td x-text="room.total_rooms"></td>
                                        <td>
                                            <button type="button" class="btn btn-link text-danger p-0" @click="removeRoom(index)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 d-flex justify-content-between">
                        <button type="button" @click="currentStep = 4" class="btn btn-light px-4">Back</button>
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold" :disabled="loading">
                            <span x-show="!loading">Finish & Save</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card">
                <h6 class="fw-bold mb-3">Live Preview</h6>
                <div class="border rounded p-3 bg-light">
                    <h5 class="fw-bold" x-text="formData.name || 'Hotel Name'"></h5>
                    <p class="text-muted small" x-text="formData.description || 'Description will appear here...'"></p>
                    <div class="d-flex gap-1 text-warning mb-2">
                        <template x-for="i in parseInt(formData.rating || 3)">
                            <i class="fas fa-star"></i>
                        </template>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary" x-text="'$' + (formData.price_per_night || 0) + '/night'"></span>
                        <span class="badge" :class="formData.status == 'active' ? 'bg-success' : 'bg-secondary'" x-text="formData.status"></span>
                    </div>
                </div>
            </div>

            <div class="admin-card mt-4">
                <h6 class="fw-bold mb-3">Need Help?</h6>
                <p class="small text-muted">Complete all steps to make your hotel live. You can save and return anytime.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('themes.hotel.admin.partials.wizard-scripts')
@endpush
