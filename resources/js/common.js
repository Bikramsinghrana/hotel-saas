// Common JS utilities: toast (SweetAlert2) and button loader helpers
window.AppCommon = (() => {
    function showToast(type, message, title = '') {
        if (typeof Swal === 'undefined') {
            console[type === 'error' ? 'error' : 'log'](message);
            return;
        }

        const icon = type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info');
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title: title || message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }

    function showLoader(btn) {
        try {
            const button = (typeof btn === 'string') ? document.querySelector(btn) : btn;
            if (!button) return;
            button.dataset._oldHtml = button.innerHTML;
            button.disabled = true;
            const spinner = '<span class="loader-spinner" style="display:inline-block;margin-right:6px;vertical-align:middle;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:rgba(255,255,255,1);border-radius:50%;animation:spin 0.8s linear infinite"></span>';
            button.innerHTML = spinner + (button.dataset.loadingText || 'Loading');
        } catch (e) { console.error(e); }
    }

    function hideLoader(btn) {
        try {
            const button = (typeof btn === 'string') ? document.querySelector(btn) : btn;
            if (!button) return;
            button.disabled = false;
            if (button.dataset._oldHtml) button.innerHTML = button.dataset._oldHtml;
            delete button.dataset._oldHtml;
        } catch (e) { console.error(e); }
    }

    // basic CSS for spinner injection
    const style = document.createElement('style');
    style.innerHTML = '@keyframes spin{to{transform:rotate(360deg)}} .loader-spinner{display:inline-block}';
    document.head.appendChild(style);

    return { showToast, showLoader, hideLoader };
})();

// Auto attach simple helper to form buttons with data-loader attribute
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-loader]');
    if (!btn) return;
    AppCommon.showLoader(btn);
});
