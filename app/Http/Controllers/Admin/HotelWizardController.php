<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelWizardRequest;
use App\Services\HotelWizardService;
use App\Repositories\HotelRepository;
use Illuminate\Http\Request;
use Exception;

class HotelWizardController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(HotelWizardService $service, HotelRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function create()
    {
        return view('admin.hotels.wizard', [
            'hotel' => null,
            'step' => 1
        ]);
    }

    public function edit($id)
    {
        $hotel = $this->repository->find($id);
        if (!$hotel) abort(404);

        return view('admin.hotels.wizard', [
            'hotel' => $hotel,
            'step' => request('step', 1)
        ]);
    }

    public function store(HotelWizardRequest $request)
    {
        try {
            $step = $request->input('step');
            $hotelId = $request->input('hotel_id');

            $result = match($step) {
                '1' => $this->service->processStep1($request->validated(), $hotelId),
                '2' => $this->service->processStep2($hotelId, $request->only(['address_line', 'city', 'state', 'country', 'pincode']), $request->input('nearby_places', [])),
                '3' => $this->service->processStep3($hotelId, $request->only(['price_per_night', 'discount_percentage', 'tax_percentage']), $request->input('facilities', [])),
                '5' => $this->service->processStep5($hotelId, $request->only(['cancellation_type', 'cancel_before_days'])),
                default => throw new Exception("Invalid step"),
            };

            return response()->json([
                'success' => true,
                'message' => "Step $step saved successfully!",
                'hotel_id' => $result->id,
                'next_url' => $step == '1' && !$hotelId 
                    ? route('admin.hotels.wizard.edit', ['id' => $result->id, 'step' => 2]) 
                    : null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function uploadMedia(Request $request, $id)
    {
        try {
            $request->validate([
                'file' => 'required|image|max:5120',
                'type' => 'required|string'
            ]);

            $media = $this->service->processStep4($id, $request->file('file'), $request->input('type'));

            return response()->json([
                'success' => true,
                'media' => $media
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function deleteMedia($id, $mediaId)
    {
        try {
            $this->repository->deleteMedia($mediaId);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function storeRoom(Request $request, $id)
    {
        try {
            $room = $this->service->addRoom($id, $request->all());
            return response()->json(['success' => true, 'room' => $room]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
