<?php

namespace Database\Seeders;

use App\Models\PageModule;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageModuleSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $tenants = [Tenant::create(['name' => 'Default Hotel', 'domain' => 'localhost'])];
        }

        foreach ($tenants as $tenant) {
            // Default Navbar Items
            PageModule::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_type' => 'navbar',
                    'slug' => 'home-nav',
                ],
                [
                    'title' => 'Home',
                    'content' => 'Welcome to our hotel booking platform',
                    'status' => 'published',
                    'is_active' => true,
                    'show_to_guest' => true,
                    'show_to_customer' => true,
                    'order' => 1,
                ]
            );

            PageModule::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_type' => 'navbar',
                    'slug' => 'about-nav',
                ],
                [
                    'title' => 'About',
                    'content' => 'Learn more about our hotel',
                    'status' => 'published',
                    'is_active' => true,
                    'show_to_guest' => true,
                    'show_to_customer' => true,
                    'order' => 2,
                ]
            );

            // Default Blog Post
            PageModule::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_type' => 'blog',
                    'slug' => 'welcome-to-our-blog',
                ],
                [
                    'title' => 'Welcome to Our Blog',
                    'description' => 'Discover travel tips, hotel updates, and exclusive offers.',
                    'content' => '<p>Welcome to our hotel blog! Here you\'ll find tips, stories, and updates about our properties and travel experiences.</p><p>Stay tuned for exciting content coming soon!</p>',
                    'status' => 'published',
                    'is_active' => true,
                    'show_to_guest' => true,
                    'show_to_customer' => true,
                    'order' => 1,
                    'meta_title' => 'Welcome to Our Blog - Hotel Updates & Travel Tips',
                    'meta_description' => 'Discover travel tips, hotel updates, and exclusive offers from our blog.',
                ]
            );

            PageModule::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_type' => 'blog',
                    'slug' => 'tips-for-best-hotel-experience',
                ],
                [
                    'title' => 'Tips for the Best Hotel Experience',
                    'description' => 'Pro tips to make your hotel stay unforgettable.',
                    'content' => '<h3>Make the Most of Your Stay</h3><p>Here are some tips to enhance your hotel experience:</p><ul><li>Arrive early for check-in</li><li>Get to know the concierge</li><li>Explore local restaurants</li><li>Take advantage of amenities</li></ul>',
                    'status' => 'published',
                    'is_active' => true,
                    'show_to_guest' => true,
                    'show_to_customer' => true,
                    'order' => 2,
                ]
            );

            // Default Sidebar Widget
            PageModule::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_type' => 'sidebar',
                    'slug' => 'special-offers',
                ],
                [
                    'title' => 'Special Offers',
                    'description' => 'Limited time deals on selected rooms',
                    'content' => '<p><strong>Early Bird Discount!</strong></p><p>Book 30 days in advance and get 20% off!</p><p><a href="/rooms" class="btn btn-primary">Browse Rooms</a></p>',
                    'status' => 'published',
                    'is_active' => true,
                    'show_to_guest' => true,
                    'show_to_customer' => true,
                    'order' => 1,
                ]
            );

            PageModule::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'module_type' => 'sidebar',
                    'slug' => 'contact-info',
                ],
                [
                    'title' => 'Contact Us',
                    'description' => 'Get in touch with our team',
                    'content' => '<p><strong>Hotel Booking Support</strong></p><p>📞 Phone: +1-800-HOTELS</p><p>📧 Email: info@hotel.com</p><p>📍 Address: 123 Main Street, Hotel City</p>',
                    'status' => 'published',
                    'is_active' => true,
                    'show_to_guest' => true,
                    'show_to_customer' => true,
                    'order' => 2,
                ]
            );
        }
    }
}
