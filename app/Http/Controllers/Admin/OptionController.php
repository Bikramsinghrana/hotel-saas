<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\Tenant;
use App\Models\Hotel;
use App\Services\OptionService;
use Illuminate\Support\Str;

class OptionController extends Controller
{
    protected $optionService;

    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }

    /**
     * Display the dynamic options management dashboard.
     */
    public function index(Request $request)
    {
        $tenant = tenant();
        $isSuperAdmin = is_super_admin();

        // If super admin and selected a tenant filter, use that
        $selectedTenantId = $request->get('tenant_id', $tenant ? $tenant->id : null);
        $selectedHotelId = $request->get('hotel_id', null);

        // Fetch hotels/properties belonging to the active tenant
        $hotels = collect();
        if ($selectedTenantId) {
            $hotels = Hotel::where('tenant_id', $selectedTenantId)->get();
        }

        // Active Group Tab ('all', 'hotel', 'restaurant', 'tax_gst', 'pagination', 'currency', 'invoice', 'general')
        $activeGroup = $request->get('group', 'all');
        $search = $request->get('search');

        // Query options
        $query = Option::query();

        // Scope: either specific tenant/hotel or global defaults
        if ($selectedHotelId) {
            $query->where('hotel_id', $selectedHotelId);
        } elseif ($selectedTenantId) {
            $query->where(function($q) use ($selectedTenantId) {
                $q->where('tenant_id', $selectedTenantId)->whereNull('hotel_id')
                  ->orWhere(fn($sq) => $sq->whereNull('tenant_id')->whereNull('hotel_id'));
            });
        } else {
            $query->whereNull('tenant_id')->whereNull('hotel_id');
        }

        if ($activeGroup !== 'all') {
            $query->where('group', $activeGroup);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $options = $query->orderBy('group')->orderBy('id')->get();

        // Group options by category for structured display
        $groupedOptions = $options->groupBy('group');

        // Available groups with metadata
        $groups = [
            'all' => ['title' => 'All Settings', 'icon' => 'fas fa-th-large', 'desc' => 'Overview of all configuration keys'],
            'hotel' => ['title' => 'Hotel & Booking', 'icon' => 'fas fa-hotel', 'desc' => 'Check-in/out times, room policies & guest limits'],
            'restaurant' => ['title' => 'Restaurant & Dining', 'icon' => 'fas fa-utensils', 'desc' => 'Table reservations, hold times & food service rules'],
            'tax_gst' => ['title' => 'Tax & GST System', 'icon' => 'fas fa-receipt', 'desc' => 'GSTIN, CGST, SGST, IGST rates & calculation mode'],
            'pagination' => ['title' => 'Pagination & Limits', 'icon' => 'fas fa-list-ol', 'desc' => 'Dynamic records per page for rooms, orders & admin tables'],
            'currency' => ['title' => 'Currency & Localization', 'icon' => 'fas fa-coins', 'desc' => 'Currency symbol, placement, precision & formatting'],
            'invoice' => ['title' => 'Invoicing & Billing', 'icon' => 'fas fa-file-invoice-dollar', 'desc' => 'Invoice prefixes, terms & customer receipt notes'],
            'general' => ['title' => 'General Information', 'icon' => 'fas fa-sliders-h', 'desc' => 'Contact details, branding & platform behaviors'],
        ];

        // Fetch all tenants if SuperAdmin
        $allTenants = $isSuperAdmin ? Tenant::with(['theme', 'subTheme'])->get() : collect();

        // Vertical type helper (hotel vs resto)
        $vertical = $tenant && $tenant->theme ? strtolower($tenant->theme->key) : 'hotel';

        return view('admin.options.index', compact(
            'groupedOptions',
            'options',
            'groups',
            'activeGroup',
            'selectedTenantId',
            'selectedHotelId',
            'hotels',
            'allTenants',
            'isSuperAdmin',
            'vertical'
        ));
    }

    /**
     * Batch save/update options submitted from a category tab.
     */
    public function batchUpdate(Request $request)
    {
        $tenant = tenant();
        $isSuperAdmin = is_super_admin();

        $tenantId = $request->get('tenant_id', $tenant ? $tenant->id : null);
        if (!$isSuperAdmin && $tenant) {
            $tenantId = $tenant->id;
        }

        $hotelId = $request->get('hotel_id') ?: null;
        $values = $request->get('options', []);

        if (empty($values) || !is_array($values)) {
            return back()->with('error', 'No options submitted to update.');
        }

        foreach ($values as $key => $val) {
            // Find reference option to know type & group
            $refOption = Option::where('key', $key)->first();
            $type = $refOption ? $refOption->type : 'string';
            $group = $refOption ? $refOption->group : 'general';

            $this->optionService->set(
                $key,
                $val,
                $group,
                $type,
                $tenantId,
                $hotelId,
                [
                    'label' => $refOption?->label,
                    'description' => $refOption?->description,
                    'is_autoload' => $refOption?->is_autoload ?? true,
                    'is_public' => $refOption?->is_public ?? false,
                    'is_system' => $refOption?->is_system ?? false,
                ]
            );
        }

        $this->optionService->clearCache($tenantId, $hotelId);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully!',
            ]);
        }

        return back()->with('success', 'Configuration settings updated successfully!');
    }

    /**
     * AJAX: Instantly update a single option key or toggle switch.
     */
    public function updateSingle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable',
        ]);

        $tenant = tenant();
        $tenantId = $request->get('tenant_id', $tenant ? $tenant->id : null);
        $hotelId = $request->get('hotel_id') ?: null;

        $key = $request->get('key');
        $val = $request->get('value');

        $refOption = Option::where('key', $key)->first();
        $type = $refOption ? $refOption->type : 'string';
        $group = $refOption ? $refOption->group : 'general';

        $option = $this->optionService->set(
            $key,
            $val,
            $group,
            $type,
            $tenantId,
            $hotelId
        );

        return response()->json([
            'success' => true,
            'message' => "Option '{$key}' updated successfully!",
            'value' => $option->typed_value,
        ]);
    }

    /**
     * Create a brand new dynamic option on the fly.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:100|regex:/^[a-zA-Z0-9_]+$/',
            'label' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'type' => 'required|in:string,number,float,boolean,json,select,color',
            'value' => 'nullable',
            'description' => 'nullable|string',
        ]);

        $tenant = tenant();
        $tenantId = $request->get('tenant_id', $tenant ? $tenant->id : null);
        $hotelId = $request->get('hotel_id') ?: null;

        $key = Str::slug($request->get('key'), '_');

        // Check for duplicate in the same scope
        $exists = Option::where('key', $key)
            ->where('tenant_id', $tenantId)
            ->where('hotel_id', $hotelId)
            ->exists();

        if ($exists) {
            return back()->with('error', "An option with key '{$key}' already exists in this scope.");
        }

        $optionsList = null;
        if ($request->get('type') === 'select' && $request->filled('options_list_raw')) {
            $raw = explode(',', $request->get('options_list_raw'));
            $optionsList = [];
            foreach ($raw as $item) {
                $trimmed = trim($item);
                if (!empty($trimmed)) {
                    $parts = explode(':', $trimmed);
                    if (count($parts) === 2) {
                        $optionsList[trim($parts[0])] = trim($parts[1]);
                    } else {
                        $optionsList[$trimmed] = ucfirst($trimmed);
                    }
                }
            }
        }

        $preparedVal = Option::prepareValue($request->get('value'), $request->get('type'));

        Option::create([
            'tenant_id' => $tenantId,
            'hotel_id' => $hotelId,
            'group' => $request->get('group'),
            'key' => $key,
            'label' => $request->get('label'),
            'value' => $preparedVal,
            'type' => $request->get('type'),
            'description' => $request->get('description'),
            'options_list' => $optionsList,
            'is_autoload' => $request->boolean('is_autoload', true),
            'is_public' => $request->boolean('is_public', false),
            'is_system' => false,
            'status' => true,
        ]);

        $this->optionService->clearCache($tenantId, $hotelId);

        return back()->with('success', "Dynamic option '{$key}' created successfully!");
    }

    /**
     * Delete a dynamic option.
     */
    public function destroy($id)
    {
        $option = Option::findOrFail($id);

        if ($option->is_system) {
            return back()->with('error', "System options are protected from deletion.");
        }

        $tenantId = $option->tenant_id;
        $hotelId = $option->hotel_id;

        $option->delete();
        $this->optionService->clearCache($tenantId, $hotelId);

        return back()->with('success', "Option '{$option->key}' deleted successfully.");
    }

    /**
     * Toggle option active status.
     */
    public function toggleStatus($id)
    {
        $option = Option::findOrFail($id);
        $option->status = !$option->status;
        $option->save();

        $this->optionService->clearCache($option->tenant_id, $option->hotel_id);

        return response()->json([
            'success' => true,
            'status' => $option->status,
            'message' => "Option status set to " . ($option->status ? 'Active' : 'Inactive'),
        ]);
    }
}
