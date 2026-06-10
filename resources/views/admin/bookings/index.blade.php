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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actionsDropdown{{ $b->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $b->id }}">
                                        <li><a class="dropdown-item" href="{{ route('admin.bookings.show', $b->id) }}">View</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.bookings.edit', $b->id) }}">Edit</a></li>
                                        @if($b->payment_status !== 'paid')
                                            <li><button class="dropdown-item row-action" data-action="mark-paid" data-url="{{ route('admin.bookings.mark-paid', $b->id) }}">Mark as paid</button></li>
                                        @endif
                                        <li><button class="dropdown-item row-action" data-action="resend-email" data-url="{{ route('admin.bookings.resend-email', $b->id) }}">Resend confirmation</button></li>
                                        @if($b->invoice)
                                            <li><a class="dropdown-item" href="{{ route('admin.payments.invoice.download', $b->invoice->id) }}">Download invoice</a></li>
                                        @endif
                                        <li>
                                            <form class="d-inline" method="POST" action="{{ route('admin.bookings.destroy', $b->id) }}" onsubmit="return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item row-action" data-action="delete" data-url="{{ route('admin.bookings.destroy', $b->id) }}">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.row-action').forEach(btn=>{
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    const action = this.getAttribute('data-action');
                    const url = this.getAttribute('data-url');
                    const original = this.innerHTML;

                    if (!action || !url) return;

                    const confirmMap = {
                        'mark-paid': { title: 'Mark booking as paid?', text: 'This will mark payment as paid and send confirmation email.' },
                        'resend-email': { title: 'Resend confirmation email?', text: 'This will resend the booking confirmation to the customer.' },
                        'delete': { title: 'Delete booking?', text: 'This action cannot be undone.' }
                    };

                    const conf = confirmMap[action] || { title: 'Proceed?', text: '' };

                    Swal.fire({ title: conf.title, text: conf.text, icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes' }).then(res=>{
                        if (!res.isConfirmed) return;

                        // show spinner
                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>...';

                        const opts = { method: action === 'delete' ? 'DELETE' : 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } };

                        fetch(url, opts).then(async r=>{
                            const data = await r.json().catch(()=>null);
                            if (r.ok && data && data.status === 'success'){
                                let msg = data.message || 'Done';
                                if (data.email_sent === false) msg += ' (email not sent)';
                                Swal.fire({ toast:true, position:'top-end', icon:'success', title: msg, showConfirmButton:false, timer:2500 });
                                setTimeout(()=>location.reload(),900);
                            } else {
                                Swal.fire({ toast:true, position:'top-end', icon:'error', title: (data && data.message) ? data.message : 'Action failed', showConfirmButton:false, timer:3500 });
                                this.disabled = false; this.innerHTML = original;
                            }
                        }).catch(()=>{
                            Swal.fire({ toast:true, position:'top-end', icon:'error', title:'Request failed', showConfirmButton:false, timer:3000 });
                            this.disabled = false; this.innerHTML = original;
                        });
                    });
                });
            });
        });
    </script>
@endpush
