@extends(\App\Helpers\HotelPath::view('layouts.app'))

@section('content')
    <div class="container">
        <div id="paymentStatusWrap">
            <h2>Processing Payment</h2>
            <p>Please wait while we confirm your payment. This page will update automatically.</p>
            <div id="statusMessage">Checking status…</div>
        </div>
        <div id="paymentCompleteWrap" style="display:none;">
            <h2>Payment Complete</h2>
            <p>Your payment was successful. You can now view your booking or download the invoice.</p>
            <div style="margin-top:1rem;">
                <a id="btnHome" href="/" class="btn btn-secondary">Go to Home</a>
                <a id="btnDashboard" href="{{ route('customer.dashboard') }}" class="btn btn-primary">My Dashboard</a>
                <a id="btnInvoice" href="#" class="btn btn-outline-primary">Download Invoice</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function(){
            const sessionId = '{{ $session_id }}';
            if (!sessionId) return;

            function checkStatus(){
                fetch("{{ route('payments.status') }}?session_id="+encodeURIComponent(sessionId), { headers: { 'X-Requested-With':'XMLHttpRequest' } })
                    .then(r => r.json()).then(data => {
                        if (data.status === 'paid') {
                            document.getElementById('paymentStatusWrap').style.display = 'none';
                            document.getElementById('paymentCompleteWrap').style.display = '';
                            if (data.invoice_id) {
                                document.getElementById('btnInvoice').setAttribute('href', '{{ url('') }}'+ '/payments/invoice/' + data.invoice_id);
                                document.getElementById('btnInvoice').style.display = '';
                            } else {
                                document.getElementById('btnInvoice').style.display = 'none';
                            }
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Payment confirmed', timer:3000, showConfirmButton:false });
                        } else if (data.status === 'pending'){
                            document.getElementById('statusMessage').innerText = 'Payment pending. We will update when payment completes.';
                            setTimeout(checkStatus, 3000);
                        } else if (data.status === 'failed'){
                            document.getElementById('statusMessage').innerText = 'Payment failed or expired.';
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Payment failed', timer:3000, showConfirmButton:false });
                        } else {
                            // not found or unknown
                            document.getElementById('statusMessage').innerText = 'Waiting for confirmation...';
                            setTimeout(checkStatus, 3000);
                        }
                    }).catch(err => { setTimeout(checkStatus, 4000); });
            }

            checkStatus();
        })();
    </script>
@endpush
