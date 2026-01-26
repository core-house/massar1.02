    // عرض آخر العمليات
    $('#recentTransactionsBtn').on('click', function() {
        const recentModal = new bootstrap.Modal(document.getElementById('recentTransactionsModal'));
        recentModal.show();
        loadRecentTransactions();
    });

    const recentTransactionsModalEl = document.getElementById('recentTransactionsModal');
    if (recentTransactionsModalEl) {
        recentTransactionsModalEl.addEventListener('show.bs.modal', function() {
            loadRecentTransactions();
        });
    }

    $('#refreshRecentTransactionsBtn').on('click', function() {
        loadRecentTransactions();
    });

    async function loadRecentTransactions() {
        const listContainer = $('#recentTransactionsList');
        listContainer.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">جاري التحميل...</p></div>');

        try {
            const response = await $.ajax({
                url: '{{ route("pos.api.recent-transactions") }}',
                method: 'GET',
                data: { limit: 50 }
            });

            if (response.success && response.transactions && response.transactions.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-hover table-striped">';
                html += `
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم الفاتورة</th>
                            <th>التاريخ</th>
                            <th>العميل</th>
                            <th>المخزن</th>
                            <th>المستخدم</th>
                            <th>عدد الأصناف</th>
                            <th>الإجمالي</th>
                            <th>المدفوع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                response.transactions.forEach((transaction, index) => {
                    const date = new Date(transaction.created_at).toLocaleString('ar-SA');
                    const printUrl = '{{ route("pos.print", ":id") }}'.replace(':id', transaction.id);
                    const showUrl = '{{ route("pos.show", ":id") }}'.replace(':id', transaction.id);
                    const editUrl = '{{ route("pos.edit", ":id") }}'.replace(':id', transaction.id);
                    
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${transaction.pro_id}</strong></td>
                            <td>${date}</td>
                            <td>${transaction.customer_name}</td>
                            <td>${transaction.store_name}</td>
                            <td>${transaction.user_name}</td>
                            <td><span class="badge bg-info">${transaction.items_count}</span></td>
                            <td><strong class="text-success">${parseFloat(transaction.total).toFixed(2)} ريال</strong></td>
                            <td>${parseFloat(transaction.paid_amount).toFixed(2)} ريال</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="${editUrl}" class="btn btn-outline-warning btn-sm" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="${showUrl}" target="_blank" class="btn btn-outline-primary btn-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="${printUrl}" target="_blank" class="btn btn-outline-secondary btn-sm" title="طباعة">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                listContainer.html(html);
            } else {
                listContainer.html(`
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد عمليات مسجلة</p>
                    </div>
                `);
            }
        } catch (err) {
            console.error('Error loading recent transactions:', err);
            listContainer.html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    حدث خطأ أثناء تحميل العمليات. يرجى المحاولة مرة أخرى.
                </div>
            `);
        }
    }
