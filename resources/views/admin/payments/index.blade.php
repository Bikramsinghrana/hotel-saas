@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Payments')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">Payments</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Gateway</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->order->order_number ?? '-' }}</td>
                        <td>{{ number_format($p->amount,2) }}</td>
                        <td>{{ ucfirst($p->status) }}</td>
                        <td>{{ $p->gateway }}</td>
                        <td>
                            <a href="{{ route('admin.payments.show', $p->id) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $payments->links() }}
    </div>
@endsection
