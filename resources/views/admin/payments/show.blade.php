@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Payment Details')

@section('content')
    <div class="container-fluid">
        <h1>Payment #{{ $payment->id }}</h1>

        <div class="mb-3">
            <strong>Order:</strong> {{ $payment->order->order_number ?? '-' }}
        </div>
        <div class="mb-3">
            <strong>Amount:</strong> {{ number_format($payment->amount,2) }}
        </div>
        <div class="mb-3">
            <strong>Status:</strong> {{ $payment->status }}
        </div>

        <form method="POST" action="{{ route('admin.payments.update_status', $payment->id) }}">
            @csrf
            <div class="input-group mb-3" style="max-width:300px;">
                <select name="status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                </select>
                <button class="btn btn-primary">Update</button>
            </div>
        </form>

        @if($payment->invoice)
            <a href="{{ route('admin.payments.invoice.download', $payment->invoice->id) }}" class="btn btn-outline-secondary">Download Invoice</a>
        @endif
    </div>
@endsection
