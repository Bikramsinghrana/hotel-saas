/**
 * Common JavaScript Utilities for Hotel SaaS
 * Handles AJAX form submissions, SweetAlert2 Delete Confirmations, and loaders.
 */

$(document).ready(function() {
    
    // Setup CSRF Token for all jQuery AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Common Toast function using SweetAlert2
    window.AppCommon = {
        showToast(message, type = 'info') {
            // lightweight fallback toast
            console.log('[toast]', type, message);
            if (window.Swal) {
                Swal.fire({ toast: true, position: 'top-end', icon: type, title: message, showConfirmButton: false, timer: 3000 });
            }
        }
    };

    /**
     * AJAX Form Submission Handler
     * Add the class 'ajax-form' to any form you want to submit via AJAX.
     * The form will automatically show a loading state on its submit button.
     */
    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();
        
        let $form = $(this);
        let url = $form.attr('action');
        let method = $form.attr('method') || 'POST';
        let formData = new FormData(this);
        
        let $submitBtn = $form.find('button[type="submit"]');
        let originalBtnText = $submitBtn.html();
        
        // Disable button and show loader
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Restore button
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                // Show Success Message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: response.message || 'Operation completed successfully.',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    if(response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.reload) {
                        window.location.reload();
                    } else {
                        $form[0].reset();
                    }
                });
            },
            error: function(xhr) {
                // Restore button
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Something went wrong. Please try again.';
                
                // Handle Laravel Validation Errors
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    errorMessage = '';
                    for (let key in errors) {
                        errorMessage += errors[key][0] + '<br>';
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessage,
                });
            }
        });
    });

    /**
     * Delete Confirmation Handler
     * Add the class 'delete-confirm' to any delete button/form.
     */
    $(document).on('click', '.delete-confirm', function(e) {
        e.preventDefault();
        
        let $element = $(this);
        // If it's a form button, get the closest form
        let $form = $element.closest('form');
        let title = $element.data('title') || 'Are you sure?';
        let text = $element.data('text') || "You won't be able to revert this!";
        
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if ($form.length > 0) {
                    // It's a form submission
                    $form.submit();
                } else {
                    // It's an AJAX link deletion (requires data-url)
                    let url = $element.data('url');
                    if (url) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message || 'Your file has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting.',
                                    'error'
                                );
                            }
                        });
                    }
                }
            }
        });
    });

});
