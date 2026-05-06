<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotelMasterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string|in:amenity,guest_service,room_facility',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'icon_or_image' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
        ];
    }
}
