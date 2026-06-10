(function(){
    function qs(sel){ return document.querySelector(sel); }
    function qsa(sel){ return Array.from(document.querySelectorAll(sel)); }

    function getCsrf(){
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function showToast(icon, title){
        if (window.Swal && typeof window.Swal.fire === 'function'){
            window.Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            // fallback
            if (icon === 'error') alert('Error: '+title); else alert(title);
        }
    }

    function buildCsvFromRows(ids){
        const header = ['Order Number','Customer','Email','Amount','Payment','Status'];
        const lines = [header.join(',')];

        ids.forEach(id=>{
            const checkbox = document.querySelector('.row-checkbox[value="'+id+'"]');
            if (!checkbox) return;
            const tr = checkbox.closest('tr');
            const tds = tr.querySelectorAll('td');
            const order = (tds[1] && tds[1].innerText.trim()) || '';
            const customerRaw = (tds[2] && tds[2].innerText.trim()) || '';
            const customerParts = customerRaw.split('\n').map(s=>s.trim()).filter(Boolean);
            const customer = customerParts[0] || '';
            const email = customerParts[1] ? customerParts[1].replace(/\s/g,'') : '';
            const amount = (tds[3] && tds[3].innerText.trim()) || '';
            const payment = (tds[4] && tds[4].innerText.trim()) || '';
            const status = (tds[5] && tds[5].innerText.trim()) || '';

            const row = [order, '"'+customer.replace(/"/g,'""')+'"', email, amount, payment, status];
            lines.push(row.join(','));
        });

        return lines.join('\n');
    }

    function downloadCsv(csv, filename){
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    }

    function init(){
        const selectAll = qs('#selectAll');
        const applyBtn = qs('#applyBulkAction');
        const bulkAction = qs('#bulkAction');
        const rowsSelector = '.row-checkbox';

        if (!applyBtn) return;

        function getSelectedIds(){
            return qsa(rowsSelector).filter(r=>r.checked).map(r=>r.value);
        }

        if (selectAll){
            selectAll.addEventListener('change', function(){
                qsa(rowsSelector).forEach(r=> r.checked = selectAll.checked);
            });
        }

        applyBtn.addEventListener('click', function(e){
            e.preventDefault();
            const action = bulkAction ? bulkAction.value : '';
            if (!action) return alert('Choose an action');
            const ids = getSelectedIds();
            if (!ids.length) return alert('No bookings selected');

            if (action === 'bulk_delete'){
                const deleteUrl = applyBtn.getAttribute('data-delete-url');
                if (!deleteUrl) return showToast('error','Delete URL not provided');

                function doDelete(){
                    fetch(deleteUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type':'application/json',
                            'X-CSRF-TOKEN': getCsrf()
                        },
                        body: JSON.stringify({ ids })
                    }).then(r=>{
                        if (!r.ok) throw new Error('Network response was not ok');
                        return r.json();
                    }).then(data=>{
                        if (data && data.status === 'success'){
                            showToast('success','Deleted successfully');
                            setTimeout(()=>location.reload(), 800);
                        } else {
                            showToast('error','Failed to delete');
                        }
                    }).catch(()=>showToast('error','Failed to delete'));
                }

                if (window.Swal && typeof window.Swal.fire === 'function'){
                    window.Swal.fire({
                        title: 'Delete selected bookings?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel'
                    }).then(res=>{ if (res.isConfirmed) doDelete(); });
                } else {
                    if (confirm('Delete selected bookings?')) doDelete();
                }
            }

            if (action === 'export_csv'){
                const csv = buildCsvFromRows(ids);
                const ts = new Date().toISOString().slice(0,19).replace(/[:T]/g,'-');
                downloadCsv(csv, 'bookings_export_'+ts+'.csv');
                showToast('success','CSV download started');
            }
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();

    // expose for debugging
    window.adminBulk = { init };
})();
