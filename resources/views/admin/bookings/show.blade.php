@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Booking Details')

@section('content')
    <div class="container-fluid">
        <h1>Booking {{ $booking->order_number }}</h1>
        <div class="card p-3 mb-3">
            <h4>Customer</h4>
            <p>{{ $booking->customer_name }}<br>{{ $booking->email }}<br>{{ $booking->phone }}</p>

            <h4>Booking</h4>
            <p>From: {{ $booking->start_date }} To: {{ $booking->end_date }}<br>Total: {{ number_format($booking->total_amount,2) }}</p>

            <h4>Payment</h4>
            <p>Status: {{ $booking->payment_status }}<br>Method: {{ $booking->payment_method }}</p>

            @if($invoice)
                <a href="{{ route('payments.invoice.download', $invoice->id) }}" class="btn btn-outline-primary">Download Invoice</a>
            @endif
        </div>
    </div>
@endsection
