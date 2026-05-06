<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\HotelStatusEnum;
use App\Enums\CancellationTypeEnum;

class HotelWizardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $step = $this->input('step');

        return match($step) {
            '1' => [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'rating' => 'required|integer|min:1|max:5',
                'status' => ['required', Rule::enum(HotelStatusEnum::class)],
            ],
            '2' => [
                'address_line' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'country' => 'required|string',
                'pincode' => 'required|string',
                'nearby_places' => 'nullable|array',
                'nearby_places.*' => 'nullable|string',
            ],
            '3' => [
                'price_per_night' => 'required|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'tax_percentage' => 'nullable|numeric|min:0|max:100',
                'facilities' => 'nullable|array',
                'facilities.*' => 'nullable|string',
            ],
            '5' => [
                'cancellation_type' => ['required', Rule::enum(CancellationTypeEnum::class)],
                'cancel_before_days' => 'required|integer|min:0',
            ],
            default => [],
        };
    }
}
