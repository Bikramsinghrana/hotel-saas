<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Feature;

class ThemeSeeder extends Seeder
{
    public function run()
    {
        // 1. Primary Industry Verticals / Themes
        $hotelTheme = Theme::updateOrCreate(
            ['key' => 'hotel'],
            [
                'name' => 'Hotel & Hospitality',
                'icon' => 'fa-hotel',
                'status' => 'active',
                'is_core' => true,
                'description' => 'Complete SaaS solution for single resorts, boutique stays, and hotel chains.'
            ]
        );

        $restaurantTheme = Theme::updateOrCreate(
            ['key' => 'restaurant'],
            [
                'name' => 'Restaurant & Food Service',
                'icon' => 'fa-utensils',
                'status' => 'active',
                'is_core' => true,
                'description' => 'End-to-end management for fine dining, cafes, bistros, and quick-service food businesses.'
            ]
        );

        // 2. Sub-Themes / Layouts
        $subThemes = [
            // Hotel Sub-Themes
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'luxury',
                'name' => 'Luxury Resort',
                'type' => 'single_hotel',
                'preview_image' => 'assets/themes/hotel/luxury/preview.jpg',
                'config_schema' => [
                    'primary_color' => '#1e3a8a',
                    'accent_color' => '#d97706',
                    'supports_sliders' => true,
                    'hero_style' => 'video_carousel'
                ],
                'status' => 'active',
                'is_premium' => true,
                'description' => 'A premium, elegant design perfect for high-end single resorts and boutique stays.'
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'budget',
                'name' => 'Budget Stay',
                'type' => 'multi_hotel',
                'preview_image' => 'assets/themes/hotel/budget/preview.jpg',
                'config_schema' => [
                    'primary_color' => '#0284c7',
                    'accent_color' => '#10b981',
                    'supports_sliders' => true,
                    'hero_style' => 'search_banner'
                ],
                'status' => 'active',
                'is_premium' => false,
                'description' => 'A clean, multi-listing design suitable for budget stays and hotel chains.'
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'boutique',
                'name' => 'Boutique Hotel',
                'type' => 'single_hotel',
                'preview_image' => 'assets/themes/hotel/boutique/preview.jpg',
                'config_schema' => [
                    'primary_color' => '#4c1d95',
                    'accent_color' => '#f43f5e',
                    'supports_sliders' => false,
                    'hero_style' => 'minimal_grid'
                ],
                'status' => 'active',
                'is_premium' => true,
                'description' => 'A cozy, aesthetic design tailored for unique boutique experiences and homestays.'
            ],

            // Restaurant Sub-Themes
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'fine_dining',
                'name' => 'Fine Dining',
                'type' => 'dine_in',
                'preview_image' => 'assets/themes/resto/fine_dining/preview.jpg',
                'config_schema' => [
                    'primary_color' => '#18181b',
                    'accent_color' => '#eab308',
                    'supports_sliders' => true,
                    'hero_style' => 'table_booking_hero'
                ],
                'status' => 'active',
                'is_premium' => true,
                'description' => 'An upscale, reservation-focused layout designed for fine dining restaurants.'
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'fast_food',
                'name' => 'Fast Food / QSR',
                'type' => 'takeaway',
                'preview_image' => 'assets/themes/resto/fast_food/preview.jpg',
                'config_schema' => [
                    'primary_color' => '#dc2626',
                    'accent_color' => '#f59e0b',
                    'supports_sliders' => true,
                    'hero_style' => 'menu_deals_carousel'
                ],
                'status' => 'active',
                'is_premium' => false,
                'description' => 'A vibrant, fast-paced layout focused on quick menu browsing and takeout ordering.'
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'cafe_bistro',
                'name' => 'Cafe & Bistro',
                'type' => 'dine_in',
                'preview_image' => 'assets/themes/resto/cafe_bistro/preview.jpg',
                'config_schema' => [
                    'primary_color' => '#78350f',
                    'accent_color' => '#10b981',
                    'supports_sliders' => true,
                    'hero_style' => 'cozy_ambiance'
                ],
                'status' => 'active',
                'is_premium' => false,
                'description' => 'A warm, inviting theme designed for coffee shops, bakeries, and artisan bistros.'
            ],
        ];

        foreach ($subThemes as $st) {
            SubTheme::updateOrCreate(
                ['theme_id' => $st['theme_id'], 'key' => $st['key']],
                $st
            );
        }

        // 3. Feature Catalog
        $features = [
            // Hotel Features
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'room_management',
                'name' => 'Room & Inventory Management',
                'description' => 'Manage hotel rooms, room types, pricing tiers, and amenities.',
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'booking_engine',
                'name' => 'Online Booking Engine',
                'description' => 'Real-time online room reservations and automated invoice generation.',
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'room_inventory',
                'name' => 'Availability Calendar',
                'description' => 'Visual calendar for room occupancy, check-in, and check-out control.',
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'coupon_system',
                'name' => 'Coupons & Discounts',
                'description' => 'Create coupon codes, percentage discounts, and seasonal offers.',
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'hotel_media_gallery',
                'name' => 'Media & Gallery Studio',
                'description' => 'High-resolution room and property gallery upload system.',
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'guest_management',
                'name' => 'Guest CRM',
                'description' => 'Manage guest profiles, stay history, and identity verification.',
            ],

            // Restaurant Features
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'table_reservation',
                'name' => 'Table Reservation',
                'description' => 'Online table booking, floor plans, and seating capacity control.',
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'menu_catalog',
                'name' => 'Digital Menu Studio',
                'description' => 'Manage dishes, menu categories, dietary badges, and price lists.',
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'kitchen_display_kds',
                'name' => 'Kitchen Display System (KDS)',
                'description' => 'Live kitchen order tracking screen for kitchen chefs.',
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'qr_ordering',
                'name' => 'Contactless QR Menu',
                'description' => 'Generate dynamic table QR codes for mobile ordering and payment.',
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'dinein_pos',
                'name' => 'Point of Sale (POS)',
                'description' => 'Quick billing and receipt printing for dine-in and takeaway.',
            ],

            // Global Features (Cross-Vertical)
            [
                'theme_id' => null,
                'key' => 'custom_domain',
                'name' => 'Custom Domain Mapping',
                'description' => 'Connect own custom domain (e.g. yourhotel.com) instead of subdomain.',
            ],
            [
                'theme_id' => null,
                'key' => 'advanced_analytics',
                'name' => 'Advanced Analytics & Reports',
                'description' => 'Detailed revenue reports, sales trends, and conversion metrics.',
            ],
            [
                'theme_id' => null,
                'key' => 'multi_user_staff',
                'name' => 'Staff Team Roles',
                'description' => 'Add multiple manager, receptionist, and staff logins with custom permissions.',
            ],
        ];

        foreach ($features as $feat) {
            Feature::updateOrCreate(
                ['key' => $feat['key']],
                [
                    'theme_id' => $feat['theme_id'],
                    'name' => $feat['name'],
                    'description' => $feat['description'],
                    'status' => 'active',
                ]
            );
        }
    }
}
