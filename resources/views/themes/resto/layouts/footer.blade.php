<footer class="resto-footer bg-black text-white pt-5 pb-3 border-top border-secondary">
    <div class="container">
        <div class="row g-4 pb-4 border-bottom border-secondary">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-utensils text-warning"></i>
                    </div>
                    <h5 class="mb-0 font-weight-bold text-white">{{ $tenant ? $tenant->name : 'Urban Bistro & Dining' }}</h5>
                </div>
                <p class="text-secondary small line-height-lg mb-3">
                    Celebrated for our culinary mastery, farm-to-table organic ingredients, and curated international wine cellars.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-xs btn-outline-secondary rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-instagram text-white"></i></a>
                    <a href="#" class="btn btn-xs btn-outline-secondary rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-facebook-f text-white"></i></a>
                    <a href="#" class="btn btn-xs btn-outline-secondary rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-yelp text-white"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white text-uppercase font-weight-bold small mb-3">Menu & Dining</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
                    <li><a href="{{ route('resto.welcome') }}#featured-menu" class="text-decoration-none text-secondary">Chef's Tasting</a></li>
                    <li><a href="{{ route('resto.welcome') }}#featured-menu" class="text-decoration-none text-secondary">Artisan Pastas</a></li>
                    <li><a href="{{ route('resto.welcome') }}#featured-menu" class="text-decoration-none text-secondary">Wood-Fired Pizzas</a></li>
                    <li><a href="{{ route('resto.welcome') }}#featured-menu" class="text-decoration-none text-secondary">Cocktail Bar</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white text-uppercase font-weight-bold small mb-3">Experiences</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
                    <li><a href="{{ route('resto.welcome') }}#table-reservation" class="text-decoration-none text-secondary">Table Bookings</a></li>
                    <li><a href="{{ route('resto.welcome') }}#experience" class="text-decoration-none text-secondary">Sommelier Cellar</a></li>
                    <li><a href="{{ route('resto.welcome') }}#experience" class="text-decoration-none text-secondary">Private Dining</a></li>
                    <li><a href="{{ route('resto.welcome') }}#experience" class="text-decoration-none text-secondary">Catering Services</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6 class="text-white text-uppercase font-weight-bold small mb-3">Hours & Location</h6>
                <p class="text-secondary small mb-2"><i class="fas fa-clock text-warning me-2"></i> Mon - Sun: 12:00 PM - 11:30 PM</p>
                <p class="text-secondary small mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i> Gourmet Square, Downtown Avenue</p>
                <p class="text-secondary small mb-0"><i class="fas fa-phone-alt text-success me-2"></i> Table Desk: +91 98765 43210</p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-3 small text-secondary">
            <div>&copy; {{ date('Y') }} {{ $tenant ? $tenant->name : 'Restaurant SaaS' }}. All rights reserved.</div>
            <div>Powered by <strong>Management SaaS</strong></div>
        </div>
    </div>
</footer>
