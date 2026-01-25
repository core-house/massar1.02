    // Save Functions
    $('#saveAndPrintBtn').on('click', function() {
        saveInvoice(true);
    });

    $('#saveOnlyBtn').on('click', function() {
        saveInvoice(false);
    });

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    async function saveInvoice(print = false) {
        const localId = generateUUID();
        
        const data = {
            local_id: localId,
            items: cart,
            customer_id: selectedCustomer,
            table: selectedTable,
            notes: invoiceNotes,
            payment_method: $('#paymentMethod').val(),
            cash_amount: $('#cashAmount').val() || 0,
            card_amount: $('#cardAmount').val() || 0
        };

        if (!isOnline) {
            try {
                const localId = await db.saveTransaction(data);
                showToast('تم الحفظ محلياً. سيتم المزامنة عند عودة الاتصال', 'info');
                const paymentModalInstance = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                if (paymentModalInstance) {
                    paymentModalInstance.hide();
                }
                cart = [];
                updateCartDisplay();
                updatePendingCount();
                return;
            } catch (err) {
                showToast('حدث خطأ أثناء الحفظ المحلي', 'error');
                return;
            }
        }

        $.ajax({
            url: '{{ route("pos.api.store") }}',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showToast('تم الحفظ بنجاح', 'success');
                if (print && response.operhead_id) {
                    const printUrl = '{{ route("pos.print", ":id") }}'.replace(':id', response.operhead_id);
                    window.open(printUrl, '_blank');
                }
                const paymentModalInstance = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                if (paymentModalInstance) {
                    paymentModalInstance.hide();
                }
                cart = [];
                selectedTable = null;
                invoiceNotes = '';
                updateCartDisplay();
                
                if (response.server_id && data.local_id) {
                    db.updateTransactionServerId(data.local_id, response.server_id).then(() => {
                        console.log('Updated server_id for local_id:', data.local_id);
                    });
                }
                
                updatePendingCount();
            },
            error: function(xhr) {
                db.saveTransaction(data).then(localId => {
                    showToast('تم الحفظ محلياً. سيتم المزامنة لاحقاً', 'warning');
                    const paymentModalInstance = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    if (paymentModalInstance) {
                        paymentModalInstance.hide();
                    }
                    cart = [];
                    updateCartDisplay();
                    updatePendingCount();
                }).catch(err => {
                    showToast('حدث خطأ أثناء الحفظ', 'error');
                });
            }
        });
    }
