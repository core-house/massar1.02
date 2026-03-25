    // Modal Triggers
    $('#registerBtn').on('click', function() {
        if (cart.length === 0) {
            alert(POS_TRANS.cart_empty_alert);
            return;
        }
        const total = calculateTotal();
        $('#paymentTotal').val(total.toFixed(2) + ' ' + POS_TRANS.currency);
        
        // تعيين قيمة المدفوع الافتراضية = إجمالي الفاتورة
        $('#cashAmount').val(total.toFixed(2));
        $('#cardAmount').val('0.00');
        
        // إعادة حساب الباقي
        calculateChange();
        
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
        
        // فوكاس على حقل المدفوع بعد فتح النافذة
        setTimeout(function() {
            $('#cashAmount').focus().select();
        }, 300);
    });

    $('#customerBtn').on('click', function() {
        const customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
        customerModal.show();
        loadCustomerBalance();
    });

    $('#selectedCustomer').on('change', function() {
        selectedCustomer = $(this).val();
        loadCustomerBalance();
    });

    function loadCustomerBalance() {
        const customerId = $('#selectedCustomer').val();
        if (!customerId) {
            $('#balanceAmount').text('0.00');
            return;
        }

        if (isOnline) {
            $.ajax({
                url: '{{ route("pos.api.customer-balance", ":id") }}'.replace(':id', customerId),
                method: 'GET',
                success: function(response) {
                    $('#balanceAmount').text(parseFloat(response.balance || 0).toFixed(2));
                },
                error: function() {
                    $('#balanceAmount').text('0.00');
                }
            });
        } else {
            $('#balanceAmount').text(POS_TRANS.balance_unavailable);
        }
    }

    $('#paymentBtn').on('click', function() {
        if (cart.length === 0) {
            alert(POS_TRANS.cart_empty_alert);
            return;
        }
        const total = calculateTotal();
        $('#paymentTotal').val(total.toFixed(2) + ' ' + POS_TRANS.currency);
        
        // تعيين قيمة المدفوع الافتراضية = إجمالي الفاتورة
        $('#cashAmount').val(total.toFixed(2));
        $('#cardAmount').val('0.00');
        
        // إعادة حساب الباقي
        calculateChange();
        
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
        
        // فوكاس على حقل المدفوع بعد فتح النافذة
        setTimeout(function() {
            $('#cashAmount').focus().select();
        }, 300);
    });

    $('#notesBtn').on('click', function() {
        const notesModal = new bootstrap.Modal(document.getElementById('notesModal'));
        notesModal.show();
    });

    // Pay Out Button
    $('#payOutBtn').on('click', function() {
        const payOutModal = new bootstrap.Modal(document.getElementById('payOutModal'));
        payOutModal.show();
        
        // فوكاس على حقل المبلغ بعد فتح النافذة
        setTimeout(function() {
            $('#payOutAmount').focus().select();
        }, 300);
    });

    // Submit Pay Out
    $('#submitPayOutBtn').on('click', function() {
        const amount = parseFloat($('#payOutAmount').val());
        const cashAccountId = $('#payOutCashAccount').val();
        const expenseAccountId = $('#payOutExpenseAccount').val();
        const description = $('#payOutDescription').val().trim();
        const notes = $('#payOutNotes').val().trim();

        // التحقق من البيانات
        if (!amount || amount <= 0) {
            alert(POS_TRANS.enter_valid_amount);
            $('#payOutAmount').focus();
            return;
        }

        if (!cashAccountId) {
            alert(POS_TRANS.select_cash_account);
            $('#payOutCashAccount').focus();
            return;
        }

        if (!expenseAccountId) {
            alert(POS_TRANS.select_expense_account);
            $('#payOutExpenseAccount').focus();
            return;
        }

        if (!description) {
            alert(POS_TRANS.enter_description);
            $('#payOutDescription').focus();
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> ' + POS_TRANS.processing);

        $.ajax({
            url: '{{ route("pos.api.petty-cash") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                amount: amount,
                cash_account_id: cashAccountId,
                expense_account_id: expenseAccountId,
                description: description,
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    alert(POS_TRANS.pay_out_success + '\n' + POS_TRANS.voucher_number_label + ' ' + response.voucher_number);
                    
                    // مسح الحقول
                    $('#payOutAmount').val('');
                    $('#payOutCashAccount').val('');
                    $('#payOutExpenseAccount').val('');
                    $('#payOutDescription').val('');
                    $('#payOutNotes').val('');
                    
                    // إغلاق النافذة
                    const payOutModal = bootstrap.Modal.getInstance(document.getElementById('payOutModal'));
                    if (payOutModal) {
                        payOutModal.hide();
                    }
                } else {
                    alert(POS_TRANS.pay_out_error + ': ' + (response.message || ''));
                }
            },
            error: function(err) {
                console.error('Error submitting pay out:', err);
                const errorMessage = err.responseJSON?.message || err.message || POS_TRANS.pay_out_error;
                alert(POS_TRANS.pay_out_error + ': ' + errorMessage);
            },
            complete: function() {
                // إعادة تفعيل الزر
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Enter key في حقل المبلغ يفتح النافذة
    $(document).on('keydown', function(e) {
        // Ctrl + P أو Cmd + P لفتح Pay Out
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            $('#payOutBtn').click();
        }
    });

    $('#tableBtn').on('click', function(e) {
        e.preventDefault();
        const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
        tableModal.show();
    });

    $('#pendingTransactionsBtn').on('click', function() {
        const pendingModal = new bootstrap.Modal(document.getElementById('pendingTransactionsModal'));
        pendingModal.show();
    });

    // Table Selection
    $('.table-btn').on('click', function() {
        selectedTable = $(this).data('table');
        $('.table-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
    });

    // Payment Method Change
    $('#paymentMethod').on('change', function() {
        const method = $(this).val();
        if (method === 'cash') {
            $('#cashAmountDiv').show();
            $('#cardAmountDiv').hide();
            $('#cashAccountDiv').show();
            $('#bankAccountDiv').hide();
            // فوكاس على حقل المبلغ النقدي
            setTimeout(function() {
                $('#cashAmount').focus().select();
            }, 100);
        } else if (method === 'card') {
            $('#cashAmountDiv').hide();
            $('#cardAmountDiv').show();
            $('#cashAccountDiv').hide();
            $('#bankAccountDiv').show();
            // فوكاس على حقل مبلغ البطاقة
            setTimeout(function() {
                $('#cardAmount').focus().select();
            }, 100);
        } else {
            $('#cashAmountDiv').show();
            $('#cardAmountDiv').show();
            $('#cashAccountDiv').show();
            $('#bankAccountDiv').show();
            // فوكاس على حقل المبلغ النقدي
            setTimeout(function() {
                $('#cashAmount').focus().select();
            }, 100);
        }
        calculateChange();
    });

    // Calculate Change
    $('#cashAmount, #cardAmount').on('input', function() {
        calculateChange();
    });

    function calculateChange() {
        const total = calculateTotal();
        const cash = parseFloat($('#cashAmount').val() || 0);
        const card = parseFloat($('#cardAmount').val() || 0);
        const paid = cash + card;
        const change = paid - total;

        if (change > 0) {
            $('#changeAmount').text(change.toFixed(2));
            $('#changeAmountDiv').show();
        } else {
            $('#changeAmountDiv').hide();
        }
    }

    // عند فتح نافذة الدفع، إظهار/إخفاء حقول الصندوق والبنك حسب طريقة الدفع
    $('#paymentModal').on('shown.bs.modal', function() {
        const method = $('#paymentMethod').val() || 'cash';
        if (method === 'cash') {
            $('#cashAccountDiv').show();
            $('#bankAccountDiv').hide();
        } else if (method === 'card') {
            $('#cashAccountDiv').hide();
            $('#bankAccountDiv').show();
        } else {
            $('#cashAccountDiv').show();
            $('#bankAccountDiv').show();
        }
    });

    // Notes Save
    $('#notesModal .btn-primary').on('click', function() {
        invoiceNotes = $('#invoiceNotes').val();
    });

    // Return Invoice Button
    $('#returnInvoiceBtn').on('click', function() {
        const returnModal = new bootstrap.Modal(document.getElementById('returnInvoiceModal'));
        returnModal.show();
        $('#returnInvoiceNumber').val('').focus();
        $('#invoiceDetails').hide();
        $('#invoiceError').hide();
    });

    // Search Invoice
    $('#searchInvoiceBtn, #returnInvoiceNumber').on('click keypress', function(e) {
        if (e.type === 'keypress' && e.which !== 13) return;
        if (e.type === 'click' || (e.type === 'keypress' && e.which === 13)) {
            e.preventDefault();
            const proId = $('#returnInvoiceNumber').val();
            if (!proId) {
                alert(POS_TRANS.enter_invoice_number);
                return;
            }

            $.ajax({
                url: '{{ route("pos.api.invoice", ":id") }}'.replace(':id', proId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const inv = response.invoice;
                        $('#invoiceInfo').html(`
                            <div class="card p-3">
                                <p><strong>${POS_TRANS.invoice_number_label}</strong> ${inv.pro_id}</p>
                                <p><strong>${POS_TRANS.invoice_date_label}</strong> ${inv.pro_date}</p>
                                <p><strong>${POS_TRANS.invoice_customer_label}</strong> ${inv.customer_name}</p>
                                <p><strong>${POS_TRANS.invoice_total_label}</strong> ${inv.total.toFixed(2)} ${POS_TRANS.currency}</p>
                                <p><strong>${POS_TRANS.invoice_items_label}</strong> ${inv.items.length}</p>
                            </div>
                        `);
                        $('#invoiceDetails').data('invoice-id', inv.id).show();
                        $('#invoiceError').hide();
                    } else {
                        $('#invoiceError').text(response.message || POS_TRANS.invoice_not_found).show();
                        $('#invoiceDetails').hide();
                    }
                },
                error: function() {
                    $('#invoiceError').text(POS_TRANS.search_error).show();
                    $('#invoiceDetails').hide();
                }
            });
        }
    });

    // Confirm Return
    $('#confirmReturnBtn').on('click', function() {
        const invoiceId = $('#invoiceDetails').data('invoice-id');
        if (!invoiceId) {
            alert(POS_TRANS.search_first);
            return;
        }

        if (!confirm(POS_TRANS.confirm_return)) {
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> ' + POS_TRANS.returning);

        $.ajax({
            url: '{{ route("pos.api.return-invoice") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                original_invoice_id: invoiceId
            },
            success: function(response) {
                if (response.success) {
                    alert(POS_TRANS.return_success + '\n' + POS_TRANS.invoice_number_label + ' ' + response.return_invoice_number);
                    const returnModal = bootstrap.Modal.getInstance(document.getElementById('returnInvoiceModal'));
                    if (returnModal) {
                        returnModal.hide();
                    }
                } else {
                    alert(POS_TRANS.return_error + ': ' + (response.message || ''));
                }
            },
            error: function(err) {
                const errorMessage = err.responseJSON?.message || err.message || POS_TRANS.return_error;
                alert(POS_TRANS.return_error + ': ' + errorMessage);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
