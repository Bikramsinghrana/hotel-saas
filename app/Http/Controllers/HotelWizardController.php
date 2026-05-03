<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelWizardController extends Controller
{
    public function step1()
    {
        return view('hotels.wizard.step1');
    }

    public function storeStep1(StoreHotelRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = session('tenant_id') ?: null;
        $hotel = Hotel::create($data);
        session(['hotel_wizard_hotel_id' => $hotel->id]);
        return redirect()->route('hotels.wizard.step2');
    }
}
