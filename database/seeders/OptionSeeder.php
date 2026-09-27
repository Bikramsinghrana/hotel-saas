<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;
use App\Models\Tenant;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstTenant = Tenant::first();
        $tenantId = $firstTenant ? $firstTenant->id : null;

        $options = [
            // ==========================================
            // 1. Tax & GST Settings
            // ==========================================
            [
                'group' => 'tax_gst',
                'key' => 'tax_enabled',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Enable Tax Calculation',
                'description' => 'Enable or disable GST/Tax calculation across bookings and orders.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'gst_rate',
                'value' => '18',
                'type' => 'float',
                'label' => 'Standard GST Rate (%)',
                'description' => 'Combined GST tax percentage rate applicable on room bookings and services.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'cgst_rate',
                'value' => '9',
                'type' => 'float',
                'label' => 'CGST Rate (%)',
                'description' => 'Central GST rate for intra-state bookings.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'sgst_rate',
                'value' => '9',
                'type' => 'float',
                'label' => 'SGST / UTGST Rate (%)',
                'description' => 'State GST or Union Territory GST rate for intra-state bookings.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'igst_rate',
                'value' => '18',
                'type' => 'float',
                'label' => 'IGST Rate (%)',
                'description' => 'Integrated GST rate for inter-state bookings.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'gstin_number',
                'value' => 'GSTIN29ABCDE1234F1Z5',
                'type' => 'string',
                'label' => 'GSTIN / Tax Identification Number',
                'description' => 'Official GST identification number printed on invoices.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'tax_calculation_type',
                'value' => 'exclusive',
                'type' => 'select',
                'label' => 'Tax Calculation Mode',
                'description' => 'Whether displayed prices include tax (inclusive) or tax is added at checkout (exclusive).',
                'options_list' => ['inclusive' => 'Inclusive of Taxes', 'exclusive' => 'Exclusive (Added at checkout)'],
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'tax_gst',
                'key' => 'service_charge_rate',
                'value' => '5',
                'type' => 'float',
                'label' => 'Service Charge (%)',
                'description' => 'Optional hotel/restaurant hospitality service charge.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],

            // ==========================================
            // 2. Dynamic Pagination Settings
            // ==========================================
            [
                'group' => 'pagination',
                'key' => 'pagination_limit',
                'value' => '12',
                'type' => 'number',
                'label' => 'Default Items Per Page',
                'description' => 'Default number of records displayed per paginated page.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'pagination',
                'key' => 'rooms_per_page',
                'value' => '9',
                'type' => 'number',
                'label' => 'Rooms Per Page (Frontend)',
                'description' => 'Number of rooms displayed per page on hotel room listings.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'pagination',
                'key' => 'orders_per_page',
                'value' => '15',
                'type' => 'number',
                'label' => 'Orders Per Page (Admin)',
                'description' => 'Number of room orders / bookings per page in admin tables.',
                'is_autoload' => true,
                'is_public' => false,
                'is_system' => false,
            ],
            [
                'group' => 'pagination',
                'key' => 'admin_table_limit',
                'value' => '20',
                'type' => 'number',
                'label' => 'Admin Table Rows Per Page',
                'description' => 'Default row limit for data tables throughout administrative dashboard.',
                'is_autoload' => true,
                'is_public' => false,
                'is_system' => false,
            ],
            [
                'group' => 'pagination',
                'key' => 'blogs_per_page',
                'value' => '6',
                'type' => 'number',
                'label' => 'Blog Posts Per Page',
                'description' => 'Number of articles displayed on the blog/news index.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],

            // ==========================================
            // 3. Currency & Localization
            // ==========================================
            [
                'group' => 'currency',
                'key' => 'currency_code',
                'value' => 'INR',
                'type' => 'string',
                'label' => 'Currency Code',
                'description' => 'Three-letter ISO currency code (e.g. INR, USD, EUR, GBP).',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'currency',
                'key' => 'currency_symbol',
                'value' => '₹',
                'type' => 'string',
                'label' => 'Currency Symbol',
                'description' => 'Currency symbol shown across prices (e.g. ₹, $, €, £).',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'currency',
                'key' => 'currency_position',
                'value' => 'prefix',
                'type' => 'select',
                'label' => 'Currency Symbol Position',
                'description' => 'Position of currency symbol before (prefix) or after (suffix) the amount.',
                'options_list' => ['prefix' => 'Before Amount (₹100)', 'suffix' => 'After Amount (100₹)'],
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'currency',
                'key' => 'decimal_places',
                'value' => '2',
                'type' => 'number',
                'label' => 'Decimal Places',
                'description' => 'Number of decimal digits shown in prices.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],

            // ==========================================
            // 4. Booking & Hotel Operations
            // ==========================================
            [
                'group' => 'booking',
                'key' => 'check_in_time',
                'value' => '14:00',
                'type' => 'string',
                'label' => 'Standard Check-in Time',
                'description' => 'Default daily check-in time for guests (e.g. 14:00 / 2:00 PM).',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'booking',
                'key' => 'check_out_time',
                'value' => '11:00',
                'type' => 'string',
                'label' => 'Standard Check-out Time',
                'description' => 'Default daily check-out time for guests (e.g. 11:00 / 11:00 AM).',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'booking',
                'key' => 'allow_guest_checkout',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Allow Guest Checkout',
                'description' => 'Enable guests to book rooms without mandatory account registration.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => true,
            ],
            [
                'group' => 'booking',
                'key' => 'free_cancellation_hours',
                'value' => '48',
                'type' => 'number',
                'label' => 'Free Cancellation Window (Hours)',
                'description' => 'Hours before check-in time during which guests can cancel for free.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'booking',
                'key' => 'max_rooms_per_booking',
                'value' => '5',
                'type' => 'number',
                'label' => 'Max Rooms Per Booking',
                'description' => 'Maximum number of rooms a single guest can reserve in one checkout.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],

            // ==========================================
            // 5. Invoicing & Billing
            // ==========================================
            [
                'group' => 'invoice',
                'key' => 'invoice_prefix',
                'value' => 'INV-',
                'type' => 'string',
                'label' => 'Invoice Prefix',
                'description' => 'Prefix added to generated invoice numbers.',
                'is_autoload' => true,
                'is_public' => false,
                'is_system' => true,
            ],
            [
                'group' => 'invoice',
                'key' => 'invoice_terms',
                'value' => 'Payment is due upon reservation confirmation. Please retain this invoice for your records.',
                'type' => 'string',
                'label' => 'Invoice Terms & Conditions',
                'description' => 'Standard terms printed at the bottom of customer invoices.',
                'is_autoload' => true,
                'is_public' => false,
                'is_system' => false,
            ],
            [
                'group' => 'invoice',
                'key' => 'invoice_footer_note',
                'value' => 'Thank you for choosing us. We hope you enjoy your luxury stay!',
                'type' => 'string',
                'label' => 'Invoice Footer Note',
                'description' => 'Friendly message printed on invoice footer.',
                'is_autoload' => true,
                'is_public' => false,
                'is_system' => false,
            ],

            // ==========================================
            // 6. Restaurant & Multi-Management Operations
            // ==========================================
            [
                'group' => 'restaurant',
                'key' => 'table_reservation_timeout_minutes',
                'value' => '15',
                'type' => 'number',
                'label' => 'Table Reservation Hold Time (Minutes)',
                'description' => 'Minutes table is held before no-show release.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'restaurant',
                'key' => 'restaurant_tax_rate',
                'value' => '5',
                'type' => 'float',
                'label' => 'F&B / Restaurant GST Rate (%)',
                'description' => 'Tax percentage rate applied to food and dining orders.',
                'is_autoload' => true,
                'is_public' => true,
                'is_system' => false,
            ],
        ];

        // Seed Global System Defaults (tenant_id = null)
        foreach ($options as $opt) {
            Option::updateOrCreate(
                [
                    'tenant_id' => null,
                    'hotel_id' => null,
                    'key' => $opt['key'],
                ],
                $opt
            );
        }

        // If a tenant exists, also seed a tenant-scoped copy
        if ($tenantId) {
            foreach ($options as $opt) {
                Option::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'hotel_id' => null,
                        'key' => $opt['key'],
                    ],
                    $opt
                );
            }
        }
    }
}
