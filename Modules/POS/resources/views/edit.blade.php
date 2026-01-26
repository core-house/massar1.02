@extends('pos::layouts.master')

@push('styles')
<style>
    .pos-edit-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1.5rem;
    }
    .transaction-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }
    .items-table {
        font-size: 0.9rem;
    }
    .items-table th {
        background: #f8f9fa;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="pos-edit-container">
    <div class="transaction-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">
                    <i class="fas fa-edit me-2"></i> تحرير معاملة POS
                </h3>
                <p class="mb-0 opacity-75">فاتورة رقم: {{ $transaction->pro_id }}</p>
            </div>
            <div>
                <a href="{{ route('pos.show', $transaction->id) }}" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-eye me-1"></i> عرض
                </a>
                <a href="{{ route('pos.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> عودة
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('pos.update', $transaction->id) }}" method="POST" id="editTransactionForm">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- معلومات أساسية -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات أساسية</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">العميل</label>
                            <select name="customer_id" id="customer_id" class="form-select">
                                <option value="">عميل نقدي</option>
                                @foreach($clientsAccounts as $client)
                                    <option value="{{ $client->id }}" 
                                        {{ ($transaction->acc1 == $client->id) ? 'selected' : '' }}>
                                        {{ $client->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المخزن</label>
                            <select name="store_id" id="store_id" class="form-select">
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" 
                                        {{ ($transaction->store_id == $store->id) ? 'selected' : '' }}>
                                        {{ $store->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الصندوق</label>
                            <select name="cash_account_id" id="cash_account_id" class="form-select">
                                <option value="">اختر الصندوق</option>
                                @foreach($cashAccounts as $cash)
                                    <option value="{{ $cash->id }}" 
                                        {{ ($transaction->acc2 == $cash->id) ? 'selected' : '' }}>
                                        {{ $cash->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الموظف</label>
                            <select name="employee_id" id="employee_id" class="form-select">
                                <option value="">اختر الموظف</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                        {{ ($transaction->emp_id == $employee->id) ? 'selected' : '' }}>
                                        {{ $employee->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الدفع -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> معلومات الدفع</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">طريقة الدفع</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="cash" {{ ($transaction->info && str_contains($transaction->info, 'نقدي')) ? 'selected' : '' }}>نقدي</option>
                                <option value="card" {{ ($transaction->info && str_contains($transaction->info, 'بطاقة')) ? 'selected' : '' }}>بطاقة</option>
                                <option value="mixed" {{ ($transaction->info && str_contains($transaction->info, 'مختلط')) ? 'selected' : '' }}>مختلط</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المبلغ النقدي</label>
                            <input type="number" name="cash_amount" id="cash_amount" 
                                   class="form-control" step="0.01" min="0" 
                                   value="{{ $transaction->paid_from_client ?? 0 }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">مبلغ البطاقة</label>
                            <input type="number" name="card_amount" id="card_amount" 
                                   class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الملاحظات</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ $transaction->info ?? $transaction->details ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الأصناف -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> الأصناف</h6>
                <button type="button" class="btn btn-sm btn-light" id="addItemBtn">
                    <i class="fas fa-plus me-1"></i> إضافة صنف
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered items-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">الصنف</th>
                                <th width="15%">الكمية</th>
                                <th width="15%">الوحدة</th>
                                <th width="15%">السعر</th>
                                <th width="15%">المجموع</th>
                                <th width="5%">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            @foreach($transaction->operationItems as $index => $item)
                            <tr data-item-id="{{ $item->item_id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <select name="items[{{ $index }}][id]" class="form-select form-select-sm item-select" required>
                                        @foreach($items as $itm)
                                            <option value="{{ $itm->id }}" 
                                                data-units='@json($itm->units)'
                                                data-prices='@json($itm->prices)'
                                                {{ $item->item_id == $itm->id ? 'selected' : '' }}>
                                                {{ $itm->name }} ({{ $itm->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity]" 
                                           class="form-control form-control-sm item-quantity" 
                                           step="0.01" min="0.01" value="{{ $item->qty_out }}" required>
                                </td>
                                <td>
                                    <select name="items[{{ $index }}][unit_id]" class="form-select form-select-sm item-unit" required>
                                        @if($item->unit)
                                            <option value="{{ $item->unit_id }}">{{ $item->unit->name }}</option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][price]" 
                                           class="form-control form-control-sm item-price" 
                                           step="0.01" min="0" value="{{ $item->item_price }}" required>
                                </td>
                                <td>
                                    <span class="item-total">{{ number_format($item->detail_value, 2) }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>الإجمالي:</strong></td>
                                <td><strong id="grandTotal">{{ number_format($transaction->fat_net ?? $transaction->pro_value ?? 0, 2) }} ريال</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('pos.show', $transaction->id) }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> إلغاء
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> حفظ التغييرات
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let itemsCache = @json($itemsData);
    let itemIndex = {{ $transaction->operationItems->count() }};

    // تحديث المجموع عند تغيير الكمية أو السعر
    $(document).on('input', '.item-quantity, .item-price', function() {
        updateItemTotal($(this).closest('tr'));
        updateGrandTotal();
    });

    // تحديث الوحدات عند تغيير الصنف
    $(document).on('change', '.item-select', function() {
        const row = $(this).closest('tr');
        const itemId = $(this).val();
        const item = itemsCache[itemId];
        
        if (item && item.units && item.units.length > 0) {
            const unitSelect = row.find('.item-unit');
            unitSelect.empty();
            item.units.forEach(unit => {
                unitSelect.append(`<option value="${unit.id}">${unit.name}</option>`);
            });
        }
        
        // تحديث السعر
        if (item && item.prices && item.prices.length > 0) {
            row.find('.item-price').val(item.prices[0].value);
        }
        
        updateItemTotal(row);
        updateGrandTotal();
    });

    // إضافة صنف جديد
    $('#addItemBtn').on('click', function() {
        const newRow = `
            <tr>
                <td>${itemIndex + 1}</td>
                <td>
                    <select name="items[${itemIndex}][id]" class="form-select form-select-sm item-select" required>
                        <option value="">اختر الصنف</option>
                        @foreach($items as $itm)
                            <option value="{{ $itm->id }}" 
                                data-units='@json($itm->units)'
                                data-prices='@json($itm->prices)'>
                                {{ $itm->name }} ({{ $itm->code }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][quantity]" 
                           class="form-control form-control-sm item-quantity" 
                           step="0.01" min="0.01" value="1" required>
                </td>
                <td>
                    <select name="items[${itemIndex}][unit_id]" class="form-select form-select-sm item-unit" required>
                        <option value="">اختر الوحدة</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][price]" 
                           class="form-control form-control-sm item-price" 
                           step="0.01" min="0" value="0" required>
                </td>
                <td>
                    <span class="item-total">0.00</span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#itemsTableBody').append(newRow);
        itemIndex++;
        updateRowNumbers();
    });

    // حذف صنف
    $(document).on('click', '.remove-item', function() {
        if (confirm('هل تريد حذف هذا الصنف؟')) {
            $(this).closest('tr').remove();
            updateRowNumbers();
            updateGrandTotal();
        }
    });

    function updateItemTotal(row) {
        const quantity = parseFloat(row.find('.item-quantity').val() || 0);
        const price = parseFloat(row.find('.item-price').val() || 0);
        const total = (quantity * price).toFixed(2);
        row.find('.item-total').text(total);
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        $('#itemsTableBody tr').each(function() {
            const total = parseFloat($(this).find('.item-total').text() || 0);
            grandTotal += total;
        });
        $('#grandTotal').text(grandTotal.toFixed(2) + ' ريال');
    }

    function updateRowNumbers() {
        $('#itemsTableBody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // التحقق من وجود أصناف قبل الإرسال
    $('#editTransactionForm').on('submit', function(e) {
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('يجب إضافة صنف واحد على الأقل');
            return false;
        }
    });
});
</script>
@endpush
@endsection
