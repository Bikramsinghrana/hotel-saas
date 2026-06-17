document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('bookingForm');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async function (e) {

        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn) {
            submitBtn.disabled = true;
        }

        try {

            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formData.get('_token')
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {

                let message = data.message || 'Something went wrong';

                if (data.errors) {

                    message = Object.values(data.errors)
                        .flat()
                        .join('<br>');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message
                });

                return;
            }

            // Cash Booking
            if (data.status === 'success') {

                Swal.fire({
                    icon: 'success',
                    title: 'Booking Created',
                    timer: 1500,
                    showConfirmButton: false
                });

                if (data.redirect) {

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }

                return;
            }

            // Stripe Payment
            if (
                data.status === 'intent' &&
                data.client_secret
            ) {

                if (!window.Stripe || !window.STRIPE_KEY) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Stripe configuration missing'
                    });

                    return;
                }

                const stripe = Stripe(window.STRIPE_KEY);

                let cardElement;

                const result = await Swal.fire({
                    title: 'Enter Card Details',
                    html: `
                        <div id="card-element" style="padding:15px;"></div>
                        <div id="card-errors"
                             style="color:red;margin-top:10px;">
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Pay Now',
                    focusConfirm: false,

                    didOpen: () => {

                        const elements = stripe.elements();

                        cardElement = elements.create('card');

                        cardElement.mount('#card-element');
                    },

                    preConfirm: async () => {

                        const paymentResult =
                            await stripe.confirmCardPayment(
                                data.client_secret,
                                {
                                    payment_method: {
                                        card: cardElement
                                    }
                                }
                            );

                        if (paymentResult.error) {

                            Swal.showValidationMessage(
                                paymentResult.error.message
                            );

                            return false;
                        }

                        return paymentResult;
                    }
                });

                if (
                    result.isConfirmed &&
                    result.value &&
                    result.value.paymentIntent
                ) {

                    const paymentIntent =
                        result.value.paymentIntent;

                    if (
                        paymentIntent.status === 'succeeded'
                    ) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {

                            window.location.href =
                                '/rooms/' +
                                data.order_id +
                                '/complete';

                        }, 1000);
                    }
                }

                return;
            }

            Swal.fire({
                icon: 'info',
                title: data.message || 'Processed'
            });

        } catch (error) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });

        } finally {

            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }
    });
});
