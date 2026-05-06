/**
 * Hotel & Room Management Dynamic JS
 */
$(document).ready(function() {
    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkDeleteBtn();
    });

    $('.row-checkbox').on('change', function() {
        toggleBulkDeleteBtn();
    });

    function toggleBulkDeleteBtn() {
        const selectedCount = $('.row-checkbox:checked').length;
        if (selectedCount > 0) {
            $('#bulkDeleteBtn').fadeIn();
        } else {
            $('#bulkDeleteBtn').fadeOut();
        }
    }

    // Bulk Delete Action
    $('#bulkDeleteBtn').on('click', function() {
        const url = $(this).data('url');
        const ids = $('.row-checkbox:checked').map(function() { return $(this).val(); }).get();

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${ids.length} items.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        ids: ids,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    // Dynamic Status Change
    window.changeStatus = function(id, status, type = 'hotel') {
        const url = type === 'hotel' ? `/admin/hotels/${id}/status` : `/admin/rooms/${id}/status`;
        
        $.ajax({
            url: url,
            type: 'POST', // Using POST with _method PATCH for compatibility
            data: {
                status: status,
                _method: 'PATCH',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (window.showToast) {
                    showToast(response.message || 'Status updated!');
                }
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to update status.', 'error');
            }
        });
    };
});
