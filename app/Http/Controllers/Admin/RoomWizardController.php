<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Tenant;
use App\Models\RoomType;
use App\Models\Term;
use App\Enums\TermTypeEnum;
use App\Services\RoomWizardService;
use App\Helpers\HotelPath;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class RoomWizardController extends Controller
{
    protected $service;

    public function __construct(RoomWizardService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $hotelId = $request->get('hotel_id') ?? (Hotel::first()->id ?? 1);
        // Create draft room and redirect to edit
        $room = Room::create([
            'tenant_id' => tenant() ? tenant()->id : 1, // Fallback if no tenant
            'hotel_id' => $hotelId,
            'room_slug' => 'room-' . uniqid(),
            'status' => 'draft',
        ]);

        return redirect()->route('admin.rooms.wizard.edit', ['id' => $room->id]);
    }

    public function edit($id)
    {
        $room = Room::with('media')->findOrFail($id);
        
        $roomTypes = RoomType::all();
        $facilities = Term::where('type', TermTypeEnum::FACILITY)->get();
        $extraServices = Term::where('type', TermTypeEnum::EXTRA_SERVICE)->get();
        $amenities = Term::where('type', TermTypeEnum::AMENITY)->get();

        return view(HotelPath::view('admin.room.wizard'), [
            'room' => $room,
            'step' => request('step', 1),
            'roomTypes' => $roomTypes,
            'facilities' => $facilities,
            'extraServices' => $extraServices,
            'amenities' => $amenities
        ]);
    }

    public function store(Request $request)
    {
        try {
            $step = (int) $request->input('step');
            $roomId = $request->input('room_id');

            // Find the room
            $room = Room::findOrFail($roomId);

            if ($step == 1) {
                $room->update($request->only([
                    'post_title', 
                    'room_slug', 
                    'room_type_id', 
                    'status', 
                    'post_content'
                ]));
            } elseif ($step == 2) {
                $room->update($request->only([
                    'total_rooms', 
                    'day', 
                    'max_adults', 
                    'max_children', 
                    'base_price', 
                    'member_price',
                    'price_per_day',
                    'discount',
                    'tax',
                    'check_in',
                    'check_out',
                    'coupon'
                ]));

                // Generate daily availability entries
                $roomService = new \App\Services\RoomService();
                $roomService->generateDailyAvailabilities($room);
            } elseif ($step == 3) {
                $room->update($request->only([
                    'facilities', 
                    'extra_services'
                ]));
            } elseif ($step == 4) { // Final review step
                $room->update(['status' => 'active']);
            }

            return response()->json([
                'success' => true,
                'message' => "Step $step saved successfully!",
                'room_id' => $room->id
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
        Log::info("Received media upload request for room $id");
        try {
            $request->validate([
                'file' => 'required|image|max:5120',
                'type' => 'required|string'
            ]);

            // Assuming Room model handles media similarly to Hotel or we can use spatie media library
            // I'll just store in local storage and add a json gallery format
            $room = Room::findOrFail($id);
            $path = $request->file('file')->store('rooms', 'public');
            
            // Basic mock media record for JS response, replace with actual media table insert if applicable
            $media = ['id' => uniqid(), 'path' => $path, 'type' => 'gallery'];
            
            $gallery = $room->gallery ?? [];
            $gallery[] = $media;
            $room->update(['gallery' => $gallery]);

            return response()->json([
                'success' => true,
                'media' => $media
            ]);
        } catch (Exception $e) {
            Log::error("Error uploading media for room $id: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function deleteMedia($id, $mediaId)
    {
        try {
            $room = Room::findOrFail($id);
            $gallery = $room->gallery ?? [];
            $gallery = array_filter($gallery, fn($m) => $m['id'] != $mediaId);
            $room->update(['gallery' => array_values($gallery)]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
