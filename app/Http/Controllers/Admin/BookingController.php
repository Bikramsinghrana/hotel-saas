<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomOrder;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomOrder::with(['room','hotel','invoice']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($r) use ($q) {
                $r->where('order_number', 'like', "%$q%")
                  ->orWhere('customer_name', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%");
            });
        }

        $bookings = $query->latest()->paginate(25)->withQueryString();
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = RoomOrder::with('room','hotel')->findOrFail($id);
        $invoice = Invoice::where('room_order_id', $booking->id)->first();
        return view('admin.bookings.show', compact('booking','invoice'));
    }

    public function edit($id)
    {
        $booking = RoomOrder::findOrFail($id);
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $booking = RoomOrder::findOrFail($id);
        $data = $request->validate([
            'customer_name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $booking->update($data);
        return redirect()->route('admin.bookings.show', $booking->id)->with('success','Booking updated');
    }

    public function destroy($id)
    {
        $booking = RoomOrder::findOrFail($id);
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success','Booking deleted');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->get('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No bookings selected'], 400);
        }

        RoomOrder::whereIn('id', $ids)->delete();
        return response()->json(['status' => 'success', 'deleted' => count($ids)]);
    }

    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $path = $request->file('csv_file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_shift($rows));

        $created = 0;
        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            if (!$data) continue;

            // basic required fields check
            if (empty($data['order_number']) || empty($data['customer_name'])) continue;

            RoomOrder::create([
                'order_number' => $data['order_number'],
                'customer_name' => $data['customer_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'total_amount' => $data['total_amount'] ?? 0,
                'status' => $data['status'] ?? 'pending',
                'payment_status' => $data['payment_status'] ?? 'pending',
            ]);
            $created++;
        }

        return redirect()->route('admin.bookings.index')->with('success', "Imported $created bookings");
    }
}
