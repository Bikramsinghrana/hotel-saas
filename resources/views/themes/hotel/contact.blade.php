@extends('themes.hotel.layouts.app')

@section('title', 'Contact Us | ' . ($tenant->name ?? 'Luxury Hotel & Resort'))

@section('content')
<!-- Hero Section -->
<section class="position-relative py-5 bg-dark text-white text-center" style="background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1600&auto=format&fit=crop') center/cover no-repeat; min-height: 340px; display: flex; align-items: center;">
    <div class="container py-4">
        <span class="badge bg-emerald-light text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold text-uppercase letter-spacing-1 mb-3">
            <i class="fas fa-headset me-1"></i> 24/7 Guest Assistance
        </span>
        <h1 class="display-4 fw-bold font-serif mb-3">Get in Touch with Us</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 650px;">
            Have questions regarding room bookings, special packages, or concierge services? We are here to assist you at any time.
        </p>
        <div class="mt-4">
            <a href="{{ url('/') }}" class="text-white-50 text-decoration-none me-2">Home</a>
            <span class="text-white-50">/</span>
            <span class="text-white fw-semibold ms-2">Contact Us</span>
        </div>
    </div>
</section>

<!-- Contact Cards & Info -->
<section class="py-5 bg-light">
    <div class="container">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 shadow-sm mb-5 d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Message Sent Successfully!</h5>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="bg-success-subtle text-success rounded-3 p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-map-marker-alt fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Our Location</h5>
                    <p class="text-muted small mb-0">
                        {{ $tenant->address ?? '108 Grand Boulevard, Royal Heritage Avenue, City Center' }}
                    </p>
                    <a href="https://maps.google.com" target="_blank" class="mt-3 text-success fw-semibold small text-decoration-none d-inline-flex align-items-center">
                        View on Google Maps <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="bg-success-subtle text-success rounded-3 p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-phone-alt fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Phone & WhatsApp</h5>
                    <p class="text-muted small mb-1">
                        <strong>Front Desk:</strong> {{ $tenant->phone ?? '+1 (800) 456-7890' }}
                    </p>
                    <p class="text-muted small mb-0">
                        <strong>VIP Concierge:</strong> +1 (800) 456-7899
                    </p>
                    <span class="badge bg-success-subtle text-success mt-3 align-self-start px-2 py-1 rounded">24/7 Available</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="bg-success-subtle text-success rounded-3 p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-envelope-open-text fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Email Support</h5>
                    <p class="text-muted small mb-1">
                        <strong>Reservations:</strong> {{ $tenant->email ?? 'stay@luxuryresort.com' }}
                    </p>
                    <p class="text-muted small mb-0">
                        <strong>Concierge:</strong> concierge@luxuryresort.com
                    </p>
                    <a href="mailto:{{ $tenant->email ?? 'stay@luxuryresort.com' }}" class="mt-3 text-success fw-semibold small text-decoration-none d-inline-flex align-items-center">
                        Write an Email <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Form and FAQ Row -->
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <h3 class="fw-bold font-serif text-dark mb-2">Send Us an Inquiry</h3>
                    <p class="text-muted small mb-4">Please complete the form below and our hospitality executive will respond within 2 hours.</p>

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0" placeholder="John Doe" required value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0" placeholder="john@example.com" required value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="tel" name="phone" class="form-control border-start-0" placeholder="+1 (555) 000-0000" value="{{ old('phone') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Subject / Inquiry Type</label>
                                <select name="subject" class="form-select">
                                    <option value="Room Booking Inquiry">Room Booking Inquiry</option>
                                    <option value="Event / Wedding Booking">Event / Wedding Booking</option>
                                    <option value="Airport Transfer Request">Airport Transfer Request</option>
                                    <option value="Dining Reservation">Dining Reservation</option>
                                    <option value="Other Feedback">Other Feedback</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">Your Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Tell us how we can assist you with your upcoming stay..." required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-success px-5 py-3 rounded-pill fw-semibold shadow-sm w-100 w-md-auto">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQs & Timings -->
            <div class="col-lg-5">
                <!-- Stay Schedule -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark font-serif mb-3"><i class="fas fa-clock text-success me-2"></i> Check-in & Dining Hours</h5>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Standard Check-in</span>
                        <strong class="text-dark small">From 2:00 PM</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Standard Check-out</span>
                        <strong class="text-dark small">Until 11:00 AM</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Buffet Breakfast</span>
                        <strong class="text-dark small">6:30 AM - 10:30 AM</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">Room Service & Butler</span>
                        <strong class="text-success small">24 Hours Daily</strong>
                    </div>
                </div>

                <!-- FAQ Accordion -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark font-serif mb-3"><i class="fas fa-question-circle text-success me-2"></i> Frequently Asked Questions</h5>
                    
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button collapsed fw-semibold text-dark px-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I request early check-in or late check-out?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    Yes, early check-in and late check-out are subject to availability. Please let our front desk team know in advance through this contact form.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed fw-semibold text-dark px-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Is airport shuttle service provided?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    We offer luxury chauffeur airport pickups and drop-offs. You can add extra transport services during checkout or request via concierge.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed fw-semibold text-dark px-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What is the cancellation policy?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    Free cancellation is available up to 48 hours before the scheduled check-in date for most standard and luxury suites.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
