@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            {{-- Processing State --}}
            <div id="paymentStatusWrap">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body text-center p-5">

                        <div class="spinner-border text-primary mb-4"
                             style="width:4rem;height:4rem;"
                             role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>

                        <h2 class="fw-bold mb-3">
                            Processing Payment
                        </h2>

                        <p class="text-muted mb-4">
                            Please wait while we securely verify your payment.
                            This page will update automatically.
                        </p>

                        <div id="statusMessage"
                             class="alert alert-info rounded-pill">
                            Checking payment status...
                        </div>

                    </div>
                </div>
            </div>

            {{-- Success State --}}
            <div id="paymentCompleteWrap" style="display:none;">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body text-center p-5">

                        <div class="mb-4">
                            <div class="bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width:90px;height:90px;">
                                <i class="fas fa-check-circle text-success fs-1"></i>
                            </div>
                        </div>

                        <h2 class="fw-bold text-success mb-3">
                            Payment Successful!
                        </h2>

                        <p class="text-muted mb-4">
                            Thank you for your booking. Your payment has been
                            received successfully and your reservation is now confirmed.
                        </p>

                        <div class="d-grid gap-2 d-md-flex justify-content-center">

                            <a href="/"
                               class="btn btn-outline-secondary px-4">
                                <i class="fas fa-home me-2"></i>
                                Home
                            </a>

                            <a href="{{ route('customer.dashboard') }}"
                               class="btn btn-primary px-4">
                                <i class="fas fa-user me-2"></i>
                                My Dashboard
                            </a>

                            <a id="btnInvoice"
                               href="#"
                               class="btn btn-success px-4">
                                <i class="fas fa-file-invoice me-2"></i>
                                Download Invoice
                            </a>

                        </div>

                    </div>
                </div>
            </div>

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
