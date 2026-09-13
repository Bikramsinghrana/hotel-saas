<?php

namespace App\Http\Controllers\Themes\Resto\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestoController extends Controller
{
    public function menuIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Food & Drinks Menu Management',
            'moduleIcon' => 'fas fa-utensils',
            'description' => 'Manage dishes, pricing, dietary tags, allergens, and recipe items.'
        ]);
    }

    public function categoriesIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Menu Categories & Sections',
            'moduleIcon' => 'fas fa-tags',
            'description' => 'Organize your restaurant menu into Appetizers, Main Course, Desserts, and Beverages.'
        ]);
    }

    public function addonsIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Modifiers & Add-ons',
            'moduleIcon' => 'fas fa-plus-circle',
            'description' => 'Configure extra toppings, portion sizes, spice levels, and combo modifiers.'
        ]);
    }

    public function tablesIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Tables & Dine-In QR Ordering',
            'moduleIcon' => 'fas fa-chair',
            'description' => 'Setup floor plans, sections (Indoor, Patio, Rooftop), and generate dynamic QR codes.'
        ]);
    }

    public function reservationsIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Table Reservations & Guest Bookings',
            'moduleIcon' => 'fas fa-calendar-alt',
            'description' => 'Accept table bookings, manage seating slots, and send SMS/WhatsApp confirmations.'
        ]);
    }

    public function posIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'POS Terminal & Cashier Billing',
            'moduleIcon' => 'fas fa-cash-register',
            'description' => 'Rapid touch POS for fast order punching, split billing, discounts, and thermal printing.'
        ]);
    }

    public function ordersIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Live Orders & Running Tickets',
            'moduleIcon' => 'fas fa-receipt',
            'description' => 'Track active dine-in, takeaway, and online delivery orders in real-time.'
        ]);
    }

    public function kdsIndex()
    {
        return view('themes.resto.admin.placeholder', [
            'moduleTitle' => 'Kitchen Display System (KDS)',
            'moduleIcon' => 'fas fa-fire-burner',
            'description' => 'Live digital kitchen display for line cooks and chefs with prep timers and item strikes.'
        ]);
    }
}
