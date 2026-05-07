/* Hotel Admin Base JS */
$(document).ready(function() {
    console.log('Hotel Admin loaded.');
    
    // Global Toast configuration
    window.showToast = function(message, icon = 'success') {
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).fire({
            icon: icon,
            title: message
        });
    };
});
