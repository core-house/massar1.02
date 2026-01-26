    // عرض الفواتير المعلقة
    $('#heldOrdersBtn').on('click', function() {
        const heldModal = new bootstrap.Modal(document.getElementById('heldOrdersModal'));
        heldModal.show();
        loadHeldOrders();
    });

    const heldOrdersModalEl = document.getElementById('heldOrdersModal');
    if (heldOrdersModalEl) {
        heldOrdersModalEl.addEventListener('show.bs.modal', function() {
            loadHeldOrders();
        });
    }

    $('#refreshHeldOrdersBtn').on('click', function() {
        loadHeldOrders();
    });

    // تحديث عدد الفواتير المعلقة في الـ badge
    function updateHeldOrdersBadge() {
        $.ajax({
            url: '{{ route("pos.api.held-orders") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const count = response.count || 0;
                    const badge = $('#heldOrdersBadge');
                    if (count > 0) {
                        badge.text(count).show();
                    } else {
                        badge.hide();
                    }
                }
            },
            error: function() {
                $('#heldOrdersBadge').hide();
            }
        });
    }

    // تحميل الفواتير المعلقة
    async function loadHeldOrders() {
        const listContainer = $('#heldOrdersList');
        listContainer.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">جاري التحميل...</p></div>');

        try {
            const response = await $.ajax({
                url: '{{ route("pos.api.held-orders") }}',
                method: 'GET'
            });

            if (response.success && response.held_orders && response.held_orders.length > 0) {
                let html = '<div class="list-group">';

                response.held_orders.forEach((order, index) => {
                    html += `
                        <div class="list-group-item list-group-item-action" style="border-radius: 10px; margin-bottom: 0.5rem; border: 1px solid #e0e0e0;">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-pause-circle text-warning me-2"></i>
                                            فاتورة #${order.id}
                                        </h6>
                                        <span class="badge bg-warning text-dark">${order.held_at_formatted}</span>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <strong>العميل:</strong> ${order.customer_name}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-store me-1"></i>
                                                <strong>المخزن:</strong> ${order.store_name}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-box me-1"></i>
                                                <strong>عدد الأصناف:</strong> ${order.items_count}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie me-1"></i>
                                                <strong>الكاشير:</strong> ${order.user_name}
                                            </small>
                                        </div>
                                    </div>
                                    ${order.notes ? `<div class="mb-2"><small class="text-muted"><i class="fas fa-sticky-note me-1"></i><strong>ملاحظات:</strong> ${order.notes}</small></div>` : ''}
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <h5 class="mb-0 text-success">
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                            ${parseFloat(order.total).toFixed(2)} ريال
                                        </h5>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm recallOrderBtn" 
                                                    data-order-id="${order.id}"
                                                    title="استدعاء الفاتورة">
                                                <i class="fas fa-redo me-1"></i> استدعاء
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-sm completeHeldOrderBtn" 
                                                    data-order-id="${order.id}"
                                                    title="إكمال الفاتورة">
                                                <i class="fas fa-check me-1"></i> إكمال
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm deleteHeldOrderBtn" 
                                                    data-order-id="${order.id}"
                                                    title="حذف الفاتورة">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                listContainer.html(html);

                // إضافة event listeners للأزرار
                $('.recallOrderBtn').on('click', function() {
                    const orderId = $(this).data('order-id');
                    recallHeldOrder(orderId);
                });

                $('.completeHeldOrderBtn').on('click', function() {
                    const orderId = $(this).data('order-id');
                    completeHeldOrder(orderId);
                });

                $('.deleteHeldOrderBtn').on('click', function() {
                    const orderId = $(this).data('order-id');
                    deleteHeldOrder(orderId);
                });
            } else {
                listContainer.html(`
                    <div class="text-center py-5">
                        <i class="fas fa-pause-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد فواتير معلقة</p>
                        <small class="text-muted">يمكنك تعليق الفواتير من نافذة الدفع</small>
                    </div>
                `);
            }
        } catch (err) {
            console.error('Error loading held orders:', err);
            listContainer.html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    حدث خطأ أثناء تحميل الفواتير المعلقة. يرجى المحاولة مرة أخرى.
                </div>
            `);
        }
    }

    // استدعاء فاتورة معلقة
    async function recallHeldOrder(orderId) {
        if (!confirm('هل تريد استدعاء هذه الفاتورة المعلقة؟ سيتم تحميلها في السلة الحالية.')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: '{{ route("pos.api.recall-order", ":id") }}'.replace(':id', orderId),
                method: 'GET'
            });

            if (response.success && response.order) {
                const order = response.order;
                
                // مسح السلة الحالية
                cart = [];
                updateCartDisplay();

                // تحميل الأصناف في السلة
                if (order.items && order.items.length > 0) {
                    order.items.forEach(item => {
                        cart.push({
                            id: item.id,
                            name: item.name || 'صنف',
                            quantity: parseFloat(item.quantity),
                            price: parseFloat(item.price),
                            unit_id: item.unit_id,
                            subtotal: parseFloat(item.quantity) * parseFloat(item.price)
                        });
                    });
                }

                // تحديث البيانات الأخرى
                if (order.customer_id) {
                    $('#selectedCustomer').val(order.customer_id).trigger('change');
                }
                if (order.store_id) {
                    $('#selectedStore').val(order.store_id).trigger('change');
                }
                if (order.cash_account_id) {
                    $('#cashAccountId').val(order.cash_account_id);
                }
                if (order.employee_id) {
                    $('#selectedEmployee').val(order.employee_id).trigger('change');
                }
                if (order.notes) {
                    $('#invoiceNotes').val(order.notes);
                }
                if (order.table_id) {
                    selectedTable = order.table_id;
                }

                // تحديث العرض
                updateCartDisplay();
                calculateTotal();

                // إغلاق النافذة
                const heldModal = bootstrap.Modal.getInstance(document.getElementById('heldOrdersModal'));
                if (heldModal) {
                    heldModal.hide();
                }

                // إظهار رسالة نجاح
                alert('تم استدعاء الفاتورة بنجاح!');
            } else {
                alert('حدث خطأ أثناء استدعاء الفاتورة');
            }
        } catch (err) {
            console.error('Error recalling held order:', err);
            alert('حدث خطأ أثناء استدعاء الفاتورة: ' + (err.responseJSON?.message || err.message));
        }
    }

    // إكمال فاتورة معلقة
    async function completeHeldOrder(orderId) {
        if (!confirm('هل تريد إكمال هذه الفاتورة المعلقة؟ سيتم حفظها كفاتورة مكتملة.')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: '{{ route("pos.api.complete-held-order", ":id") }}'.replace(':id', orderId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                alert('تم إكمال الفاتورة بنجاح! رقم الفاتورة: ' + response.invoice_number);
                loadHeldOrders();
                updateHeldOrdersBadge();
            } else {
                alert('حدث خطأ أثناء إكمال الفاتورة: ' + (response.message || 'خطأ غير معروف'));
            }
        } catch (err) {
            console.error('Error completing held order:', err);
            alert('حدث خطأ أثناء إكمال الفاتورة: ' + (err.responseJSON?.message || err.message));
        }
    }

    // حذف فاتورة معلقة
    async function deleteHeldOrder(orderId) {
        if (!confirm('هل أنت متأكد من حذف هذه الفاتورة المعلقة؟ لا يمكن التراجع عن هذا الإجراء.')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: '{{ route("pos.api.delete-held-order", ":id") }}'.replace(':id', orderId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                alert('تم حذف الفاتورة المعلقة بنجاح');
                loadHeldOrders();
                updateHeldOrdersBadge();
            } else {
                alert('حدث خطأ أثناء حذف الفاتورة: ' + (response.message || 'خطأ غير معروف'));
            }
        } catch (err) {
            console.error('Error deleting held order:', err);
            alert('حدث خطأ أثناء حذف الفاتورة: ' + (err.responseJSON?.message || err.message));
        }
    }

    // تعليق الفاتورة
    $('#holdOrderBtn').on('click', function() {
        if (cart.length === 0) {
            alert('السلة فارغة. لا يمكن تعليق فاتورة فارغة.');
            return;
        }

        if (!confirm('هل تريد تعليق هذه الفاتورة؟ يمكنك استدعاؤها لاحقاً من قائمة الفواتير المعلقة.')) {
            return;
        }

        const total = calculateTotal();
        const customerId = $('#selectedCustomer').val() || null;
        const storeId = $('#selectedStore').val() || null;
        const cashAccountId = $('#cashAccountId').val() || null;
        const bankAccountId = $('#bankAccountId').val() || null;
        const employeeId = $('#selectedEmployee').val() || null;
        const paymentMethod = $('#paymentMethod').val() || 'cash';
        const cashAmount = parseFloat($('#cashAmount').val()) || 0;
        const cardAmount = parseFloat($('#cardAmount').val()) || 0;
        const notes = $('#invoiceNotes').val() || '';
        const tableId = selectedTable || null;

        // تحضير بيانات الأصناف
        const items = cart.map(item => ({
            id: item.id,
            quantity: item.quantity,
            price: item.price,
            unit_id: item.unit_id || null
        }));

        $.ajax({
            url: '{{ route("pos.api.hold-order") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                items: items,
                customer_id: customerId,
                store_id: storeId,
                cash_account_id: cashAccountId,
                bank_account_id: bankAccountId,
                employee_id: employeeId,
                payment_method: paymentMethod,
                cash_amount: cashAmount,
                card_amount: cardAmount,
                notes: notes,
                table_id: tableId
            },
            success: function(response) {
                if (response.success) {
                    alert('تم تعليق الفاتورة بنجاح!');
                    
                    // مسح السلة
                    cart = [];
                    updateCartDisplay();
                    calculateTotal();
                    
                    // إغلاق نافذة الدفع
                    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    if (paymentModal) {
                        paymentModal.hide();
                    }
                    
                    // تحديث badge
                    updateHeldOrdersBadge();
                } else {
                    alert('حدث خطأ أثناء تعليق الفاتورة: ' + (response.message || 'خطأ غير معروف'));
                }
            },
            error: function(err) {
                console.error('Error holding order:', err);
                alert('حدث خطأ أثناء تعليق الفاتورة: ' + (err.responseJSON?.message || err.message));
            }
        });
    });

    // تحديث badge عند تحميل الصفحة
    $(document).ready(function() {
        updateHeldOrdersBadge();
        // تحديث badge كل 30 ثانية
        setInterval(updateHeldOrdersBadge, 30000);
    });
