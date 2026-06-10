@extends(\App\Helpers\HotelPath::view('layouts.admin'))

@section('title', 'Bookings')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Bookings</h1>
        </div>

        <div class="mb-2 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <select id="bulkAction" class="form-select form-select-sm">
                        <option value="">Bulk actions</option>
                        <option value="bulk_delete">Delete selected</option>
                        <option value="export_csv">Export CSV</option>
                    </select>
                </div>
                <div class="me-3">
                    <button id="applyBulkAction" class="btn btn-sm btn-secondary" data-delete-url="{{ route('admin.bookings.bulk-delete') }}">Apply</button>
                </div>

                <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-2">
                    <div class="col-auto">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search order, name, email">
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All status</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="">All payments</option>
                            <option value="pending" {{ request('payment_status')=='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status')=='paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status')=='failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="{{ $b->id }}"></td>
                            <td>{{ $b->order_number }}</td>
                            <td>{{ $b->customer_name }}<br><small>{{ $b->email }}</small></td>
                            <td>{{ number_format($b->total_amount,2) }}</td>
                            <td>{{ ucfirst($b->payment_status ?? 'pending') }}</td>
                            <td>{{ ucfirst($b->status ?? '') }}</td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('admin.bookings.edit', $b->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @if($b->invoice)
                                    <a href="{{ route('admin.payments.invoice.download', $b->invoice->id) }}" class="btn btn-sm btn-info">Invoice</a>
                                @endif
                                <form action="{{ route('admin.bookings.destroy', $b->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $bookings->links() }}
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin-bulk.js') }}"></script>
@endpush
