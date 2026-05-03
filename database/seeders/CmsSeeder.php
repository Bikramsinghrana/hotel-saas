<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\Tenant;

class CmsSeeder extends Seeder
{
    public function run()
    {
        $tenant = Tenant::first();

        // Create homepage
        $page = Page::firstOrCreate([
            'tenant_id' => $tenant?->id,
            'slug' => 'home',
        ], [
            'title' => 'Homepage',
            'type' => 'home',
            'status' => 'published',
            'settings' => ['hero_enabled' => true],
        ]);

        // Hero section
        $hero = Section::firstOrCreate(['page_id' => $page->id, 'key' => 'hero'], [
            'name' => 'Hero', 'position' => 0,
            'settings' => ['layout' => 'center']
        ]);

        SectionItem::firstOrCreate(['section_id' => $hero->id, 'position' => 0], [
            'type' => 'hero',
            'content' => ['heading' => 'Welcome to Demo Hotels','subheading' => 'Book the best stays'],
        ]);

        // Featured hotels section
        $featured = Section::firstOrCreate(['page_id' => $page->id, 'key' => 'featured_hotels'], [
            'name' => 'Featured Hotels', 'position' => 1,
        ]);

        SectionItem::firstOrCreate(['section_id' => $featured->id, 'position' => 0], [
            'type' => 'featured_list',
            'content' => ['limit' => 6],
        ]);

        // Testimonials
        $test = Section::firstOrCreate(['page_id' => $page->id, 'key' => 'testimonials'], [
            'name' => 'Testimonials', 'position' => 2,
        ]);

        SectionItem::firstOrCreate(['section_id' => $test->id, 'position' => 0], [
            'type' => 'testimonials',
            'content' => ['items' => [['author' => 'Jane', 'text' => 'Great stay!']]],
        ]);
    }
}
