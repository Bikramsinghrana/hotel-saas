<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $step = (int) $this->input('step');

        return match ($step) {
            1 => [
                'post_title' => 'required|string|max:255',
                'room_slug' => 'required|string|max:255',
                'room_type_id' => 'required|exists:room_types,id',
                'status' => 'required|in:pending,draft,active,inactive',
                'post_content' => 'nullable|string',
            ],
            2 => [
                'total_rooms' => 'required|integer|min:1',
                'max_adults' => 'required|integer|min:1',
                'max_children' => 'nullable|integer|min:0',
                'base_price' => 'required|numeric|min:0',
                'member_price' => 'nullable|numeric|min:0',
                'price_per_day' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0|max:100',
                'tax' => 'nullable|numeric|min:0',
                'check_in' => 'nullable|date',
                'check_out' => 'nullable|date|after_or_equal:check_in',
                'coupon' => 'nullable|string|max:50',
            ],
            3 => [
                'facilities' => 'nullable|array',
                'extra_services' => 'nullable|array',
            ],
            4 => [
                'accept_terms' => 'accepted',
            ],
            default => [],
        };
    }

    public function messages()
    {
        return [
            'accept_terms.accepted' => 'You must accept the terms and conditions to proceed.',
            'room_type_id.required' => 'Please select a room type.',
            'post_title.required' => 'Room name is required.',
        ];
    }
}
