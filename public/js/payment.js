document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bookingForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const action = form.getAttribute('action');

        fetch(action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token')
            },
            body: formData
        }).then(async (res) => {
            const data = await res.json();
            if (!res.ok) {
                const msg = (data && data.message) ? data.message : 'An error occurred';
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, timer: 5000, showConfirmButton: false });
                return;
            }

            if (data.status === 'redirect' && data.url) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Redirecting to payment...', timer: 1500, showConfirmButton: false });
                window.location.href = data.url;
                return;
            }

            // New intent flow: open Stripe Elements modal to collect card and confirm
            if (data.status === 'intent' && data.client_secret) {
                // ensure Stripe key exists on page
                if (!window.Stripe || !window.STRIPE_KEY) {
                    Swal.fire({ toast:true, position:'top-end', icon:'error', title:'Stripe not configured on page', timer:5000, showConfirmButton:false });
                    return;
                }

                const clientSecret = data.client_secret;
                const orderId = data.order_id;
                const stripe = Stripe(window.STRIPE_KEY);

                // build modal
                const modalHtml = `
                <div id="cardModal" class="swal2-container swal2-center">
                    <div id="cardElementContainer" style="padding:20px;">
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert" style="color:red;margin-top:10px;"></div>
                    </div>
                </div>`;

                Swal.fire({
                    title: 'Enter card details',
                    html: '<div id="card-element" style="margin-top:10px;"></div><div id="card-errors" role="alert" style="color:red;margin-top:10px;"></div>',
                    showCancelButton: true,
                    confirmButtonText: 'Pay',
                    didOpen: () => {
                        const elements = stripe.elements();
                        const style = { base: { color: '#32325d', fontSize: '16px' } };
                        const card = elements.create('card', { style });
                        card.mount('#card-element');
                        window.__stripe_card = card;
                    },
                    preConfirm: () => {
                        const card = window.__stripe_card;
                        return stripe.confirmCardPayment(clientSecret, { payment_method: { card: card } }).then(function(result){
                            if (result.error) {
                                Swal.showValidationMessage(result.error.message || 'Payment failed');
                                return false;
                            }
                            return result;
                        });
                    }
                }).then((result)=>{
                        if (result.isConfirmed && result.value && result.value.paymentIntent && result.value.paymentIntent.status === 'succeeded'){
                        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Payment succeeded', timer:2500, showConfirmButton:false });
                        // Redirect to booking complete page for the order (web route: /rooms/{order}/complete)
                        setTimeout(()=>{ window.location.href = '/rooms/' + orderId + '/complete'; }, 1200);
                    }
                }).finally(()=>{
                    if (window.__stripe_card) { window.__stripe_card.unmount(); delete window.__stripe_card; }
                });

                return;
            }

            if (data.status === 'success') {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Booking created. Redirecting...', timer: 1500, showConfirmButton: false });
                if (data.redirect) {
                    setTimeout(() => { window.location.href = data.redirect; }, 1000);
                }
                return;
            }

            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: data.message || 'Processed', timer: 3000, showConfirmButton: false });
        }).catch((err) => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: err.message || 'Network error', timer: 5000, showConfirmButton: false });
        });
    });
});
