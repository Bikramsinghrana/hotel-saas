<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomAvailability;
use App\Models\RoomOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoomService
{
    /**
     * Search for available rooms based on filters.
     *
     * @param array $filters
     * @param int|null $tenantId
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function searchAvailableRooms($filters = [], $tenantId = null)
    {
        $query = Room::query()->where('status', 'active');

        // Apply Tenant Filter
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        // Filter by Hotel/Room Name
        if (!empty($filters['name'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('room_slug', 'like', '%' . $filters['name'] . '%')
                  ->orWhere('room_type', 'like', '%' . $filters['name'] . '%');
            });
        }

        // Filter by Room Type ID
        if (!empty($filters['room_type_id'])) {
            $query->where('room_type_id', $filters['room_type_id']);
        }

        // Date & Guest Availability Filter
        $checkIn = !empty($filters['check_in']) ? Carbon::parse($filters['check_in']) : null;
        $checkOut = !empty($filters['check_out']) ? Carbon::parse($filters['check_out']) : null;
        $adults = !empty($filters['adults']) ? (int) $filters['adults'] : 1;
        $children = !empty($filters['children']) ? (int) $filters['children'] : 0;
        $totalRoomsNeeded = !empty($filters['total_rooms']) ? (int) $filters['total_rooms'] : 1;

        // Ensure room can physically hold the requested guests per room
        $query->where('max_adults', '>=', $adults);
        $query->where('max_children', '>=', $children);

        if ($checkIn && $checkOut) {
            $query->where(function ($q) use ($checkIn, $checkOut, $totalRoomsNeeded) {
                // 1. Check against physical Room table total_rooms and overlapping orders
                $q->whereDoesntHave('orders', function ($orderQuery) use ($checkIn, $checkOut) {
                    $orderQuery->where(function ($dateQuery) use ($checkIn, $checkOut) {
                        $dateQuery->where('start_date', '<', $checkOut)
                                  ->where('end_date', '>', $checkIn);
                    })
                    ->whereIn('status', ['pending', 'confirmed', 'paid']);
                });

                // 2. CHECK room_availabilities table DAILY (as requested)
                // The room must NOT have any day in the range where (total_room - booked) < totalRoomsNeeded
                $q->whereDoesntHave('availabilities', function ($availQuery) use ($checkIn, $checkOut, $totalRoomsNeeded) {
                    $availQuery->where('check_in', '>=', $checkIn->format('Y-m-d'))
                               ->where('check_in', '<', $checkOut->format('Y-m-d'))
                               ->whereRaw('(total_room - booked) < ?', [$totalRoomsNeeded]);
                });
            });
        }

        // Return paginated results
        return $query->latest()->paginate(12)->appends($filters);
    }

    /**
     * Update the booked count in room_availabilities table (Daily logic).
     *
     * @param int $roomId
     * @param string $checkIn
     * @param string $checkOut
     * @param int $quantity
     * @return void
     */
    public function updateRoomBookedCount($roomId, $checkIn, $checkOut, $quantity = 1)
    {
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);

        // For each day in the booking range, increment the booked count
        // We use check_in <= date < check_out logic
        for ($date = $checkInDate->copy(); $date->lt($checkOutDate); $date->addDay()) {
            RoomAvailability::where('post_id', $roomId)
                ->whereDate('check_in', $date->format('Y-m-d'))
                ->increment('booked', $quantity);
        }
    }

    /**
     * Generate daily availability rows for a room range.
     *
     * @param Room $room
     * @return void
     */
    public function generateDailyAvailabilities(Room $room)
    {
        if (!$room->check_in || !$room->check_out) return;

        $start = Carbon::parse($room->check_in);
        $end = Carbon::parse($room->check_out);

        // Clear existing daily entries for this room within this range to avoid duplicates
        RoomAvailability::where('post_id', $room->id)
            ->whereBetween('check_in', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->delete();

        $data = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $data[] = [
                'tenant_id' => $room->tenant_id,
                'post_id' => $room->id,
                'hotel_id' => $room->hotel_id,
                'total_room' => $room->total_rooms,
                'adult_number' => $room->max_adults,
                'child_number' => $room->max_children,
                'check_in' => $date->format('Y-m-d'),
                'check_out' => $date->format('Y-m-d'), // Daily entry: in and out same day
                'price' => $room->price_per_day,
                'booked' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($data)) {
            RoomAvailability::insert($data);
        }
    }
}
