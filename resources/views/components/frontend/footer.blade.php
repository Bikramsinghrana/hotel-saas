
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div>
                <a href="/" class="footer-brand">{{ config('app.name', 'LuxuryBo') }}</a>
                <p class="footer-desc">Experience world-class hospitality at its finest. Discover luxury accommodations and exceptional service tailored just for you.</p>
            </div>
            <div>
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/#rooms">Rooms</a></li>
                    <li><a href="/#amenities">Amenities</a></li>
                    <li><a href="/#book">Book Now</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Services</h4>
                <ul class="footer-links">
                    <li><a href="#">Room Service</a></li>
                    <li><a href="#">Spa & Wellness</a></li>
                    <li><a href="#">Fine Dining</a></li>
                    <li><a href="#">Concierge</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Contact Info</h4>
                <ul class="footer-links">
                    <li><a href="#">123 Luxury Avenue</a></li>
                    <li><a href="#">+1 (800) 123-4567</a></li>
                    <li><a href="#">info@luxurybo.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'LuxuryBo') }}. All rights reserved.</span>
            <span>Built with Laravel &mdash; PHP v{{ PHP_VERSION }}</span>
        </div>
    </div>
</footer>
