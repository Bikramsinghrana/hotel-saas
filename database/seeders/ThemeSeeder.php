<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Models\SubTheme;

class ThemeSeeder extends Seeder
{
    public function run()
    {
        // Primary Theme
        $hotelTheme = Theme::firstOrCreate(
            ['key' => 'hotel'],
            ['name' => 'Hotel','status' => 'active', 'description' => 'Standard Hotel SaaS Theme']
        );

        $restaurantTheme = Theme::firstOrCreate(
            ['key' => 'restaurant'],
            ['name' => 'Restaurant', 'status' => 'inactive', 'description' => 'Restaurant & Cafe Theme']
        );

        // Hotel Sub Themes
        $subThemes = [
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'luxury',
                'name' => 'Luxury Resort',
                'type' => 'single_hotel',
                'description' => 'A premium, elegant design perfect for high-end single resorts.'
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'budget',
                'name' => 'Budget Stay',
                'type' => 'multi_hotel',
                'description' => 'A clean, multi-listing design suitable for hotel chains.'
            ],
            [
                'theme_id' => $hotelTheme->id,
                'key' => 'boutique',
                'name' => 'Boutique Hotel',
                'type' => 'single_hotel',
                'description' => 'A cozy, aesthetic design tailored for unique boutique experiences.'
            ],
            [
                'theme_id' => $restaurantTheme->id,
                'key' => 'fine_dining',
                'name' => 'Fine Dining',
                'type' => 'restaurant',
                'description' => 'An upscale, reservation-focused theme for restaurants.'
            ],
        ];

        foreach ($subThemes as $st) {
            SubTheme::firstOrCreate(
                ['theme_id' => $st['theme_id'], 'key' => $st['key']],
                [
                    'name' => $st['name'],
                    'type' => $st['type'], 
                    'description' => $st['description']
                ]
            );
        }
    }
}
