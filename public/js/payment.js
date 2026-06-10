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
