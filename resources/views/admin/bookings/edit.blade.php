@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Edit Booking')

@section('content')
    <div class="container-fluid">
        <h1>Edit Booking {{ $booking->order_number }}</h1>

        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" value="{{ $booking->customer_name }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $booking->email }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ $booking->phone }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="pending" {{ $booking->status=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $booking->status=='confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ $booking->status=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ $booking->status=='completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Status</label>
                <select name="payment_status" class="form-control">
                    <option value="pending" {{ $booking->payment_status=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $booking->payment_status=='paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ $booking->payment_status=='failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $booking->payment_status=='refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <button class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
