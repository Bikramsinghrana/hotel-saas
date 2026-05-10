<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveHotelRequest;
use App\Services\HotelWizardService;
use App\Repositories\HotelRepository;
use App\Enums\HotelStatusEnum;
use App\Helpers\HotelPath;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

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
        $hotel = \App\Models\Hotel::create([
            'tenant_id' => tenant()->id,
            'name' => 'New Hotel ' . rand(100, 999),
            'status' => HotelStatusEnum::PENDING,
            'address' => [],
            'facilities' => [],
            'extra_info' => [],
            'rating' => 1
        ]);
        // dd($hotel);

        return redirect()->route('admin.hotels.wizard.edit', ['id' => $hotel->id]);
    }

    public function edit($id)
    {  
        $hotel = $this->repository->find($id);
        if (!$hotel) abort(404);

        $response =  view(HotelPath::view('admin.management.wizard'), [
            'hotel' => $hotel,
            'step' => request('step', 1)
        ]);
        // dd($response);
        return $response;
    }

    public function store(SaveHotelRequest $request)
    {
        try {
            $step = (int) $request->input('step');
            $hotelId = $request->input('hotel_id');

            $result = match ($step) {
                1 => $this->service->processStep1($request->validated(), $hotelId),
                2 => $this->service->processStep2($hotelId, $request->only(['address_line', 'city', 'state', 'country', 'pincode']), $request->input('nearby_places', [])),
                3 => $this->service->processStep3($hotelId, $request->only(['price_per_night', 'discount_percentage', 'tax_percentage']), $request->input('facilities', [])),
                5 => $this->service->processStep5($hotelId, $request->only(['cancellation_type', 'cancel_before_days'])),
                default => throw new \Exception("Invalid step"),
            };

            if ($step === 5) {
                $result->update(['status' => \App\Enums\HotelStatusEnum::ACTIVE]);
            }

            return response()->json([
                'success' => true,
                'message' => "Step $step saved successfully!",
                'hotel_id' => $result->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function uploadMedia(Request $request, $id)
    {   
        Log::info("Received media upload request for hotel $id with data: " . json_encode($request->all()));
        try {
            $request->validate([
                'file' => 'required|image|max:5120',
                'type' => 'required|string'
            ]);

            $media = $this->service->processStep4($id, $request->file('file'), $request->input('type'));
            Log::info("Media uploaded for hotel $id: " . json_encode($media));  
            return response()->json([
                'success' => true,
                'media' => $media
            ]);
        } catch (Exception $e) {
            Log::error("Error uploading media for hotel $id: " . $e->getMessage());
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
