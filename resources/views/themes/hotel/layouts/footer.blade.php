<footer class="hotel-footer bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4 pb-4 border-bottom border-secondary">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width: 40px; height: 40px; background: rgba(59,130,246,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hotel text-primary"></i>
                    </div>
                    <h5 class="mb-0 font-weight-bold text-white">{{ $tenant ? $tenant->name : 'Grand Palace Resort' }}</h5>
                </div>
                <p class="text-secondary small line-height-lg mb-3">
                    Experience world-class hospitality, tranquil suites, bespoke guest services, and breathtaking scenic waterfront views.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-xs btn-outline-light rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-xs btn-outline-light rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-xs btn-outline-light rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-tripadvisor"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white text-uppercase font-weight-bold small mb-3">Accommodations</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
                    <li><a href="{{ route('hotel.welcome') }}#accommodations" class="text-decoration-none text-secondary">Royal Suites</a></li>
                    <li><a href="{{ route('hotel.welcome') }}#accommodations" class="text-decoration-none text-secondary">Deluxe Ocean Rooms</a></li>
                    <li><a href="{{ route('hotel.welcome') }}#accommodations" class="text-decoration-none text-secondary">Family Villas</a></li>
                    <li><a href="{{ route('hotel.welcome') }}#offers" class="text-decoration-none text-secondary">Member Offers</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white text-uppercase font-weight-bold small mb-3">Experiences</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
                    <li><a href="{{ route('hotel.welcome') }}#why-choose-us" class="text-decoration-none text-secondary">Wellness & Spa</a></li>
                    <li><a href="{{ route('hotel.welcome') }}#why-choose-us" class="text-decoration-none text-secondary">Infinity Pool</a></li>
                    <li><a href="{{ route('hotel.welcome') }}#why-choose-us" class="text-decoration-none text-secondary">Fine Dining</a></li>
                    <li><a href="{{ route('hotel.welcome') }}#why-choose-us" class="text-decoration-none text-secondary">Concierge 24/7</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6 class="text-white text-uppercase font-weight-bold small mb-3">Concierge & Bookings</h6>
                <p class="text-secondary small mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i> Prime Waterfront Boulevard, Seaside Bay</p>
                <p class="text-secondary small mb-2"><i class="fas fa-phone-alt text-primary me-2"></i> +91 1800 234 5678</p>
                <p class="text-secondary small mb-0"><i class="fas fa-envelope text-warning me-2"></i> reservations@management-saas.in</p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-3 small text-secondary">
            <div>&copy; {{ date('Y') }} {{ $tenant ? $tenant->name : 'Hotel SaaS' }}. All rights reserved.</div>
            <div>Powered by <strong>Management SaaS</strong></div>
        </div>
    </div>
</footer>
