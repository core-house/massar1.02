@extends('admin.dashboard')


@push('styles')
    <style>
        .inventory-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .discrepancy-shortage {
            background-color: #fff5f5;
            border-left: 4px solid #e53e3e;
        }

        .discrepancy-overage {
            background-color: #f0f9ff;
            border-left: 4px solid #3182ce;
        }

        .discrepancy-match {
            background-color: #f0fff4;
            border-left: 4px solid #38a169;
        }

        .badge-shortage {
            background-color: #fed7d7;
            color: #c53030;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-overage {
            background-color: #bee3f8;
            color: #2b6cb0;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-match {
            background-color: #c6f6d5;
            color: #2f855a;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .print-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .print-section {
                box-shadow: none;
                border: none;
            }

            body {
                background: white;
            }
        }

        .quantity-input {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            width: 100px;
            text-align: center;
        }

        .quantity-input:focus {
            border-color: #4299e1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }
    </style>
@endpush

@section('content')
    @include('components.breadcrumb', [
        'title' => __('تقرير جرد الأصناف'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('التقارير'), 'url' => route('reports.index')],
            ['label' => __('تقرير جرد الأصناف')],
        ],
    ])
    <div class="container-fluid">
        <!-- فلاتر التقرير -->
        <div class="row no-print mb-4">
            <div class="col-12">
                <div class="card inventory-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-filter-3-line"></i> فلاتر التقرير
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ request()->url() }}" id="inventoryForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">المخزن</label>
                                    <select name="warehouse_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">جميع المخازن</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}"
                                                {{ $selectedWarehouse == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->aname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label"> </label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary" onclick="updateAllQuantities()">
                                            <i class="ri-save-line"></i> تحديث الكل
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="window.print()">
                                            <i class="ri-printer-line"></i> طباعة
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول البيانات -->
        <div class="row">
            <div class="col-12">
                <div class="card inventory-card print-section">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="25%">اسم الصنف</th>
                                        <th width="12%">الكمية في النظام</th>
                                        <th width="12%">الكمية المتوقعة</th>
                                        <th width="12%">الكمية الفعلية</th>
                                        <th width="10%">الفرق</th>
                                        <th width="8%">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inventoryData as $index => $data)
                                        @php
                                            $rowClass = '';
                                            if ($data['discrepancy'] < 0) {
                                                $rowClass = 'discrepancy-shortage';
                                            } elseif ($data['discrepancy'] > 0) {
                                                $rowClass = 'discrepancy-overage';
                                            } else {
                                                $rowClass = 'discrepancy-match';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}" data-item-id="{{ $data['item']->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $data['item']->name }}</strong>
                                                @if ($data['item']->code)
                                                    <br><small class="text-muted">كود: {{ $data['item']->code }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span>
                                                    {{ number_format($data['system_quantity'], 2) }}
                                                    {{ $data['main_unit']->name ?? 'وحدة' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span>
                                                    {{ number_format($data['expected_quantity'], 2) }}
                                                    {{ $data['main_unit']->name ?? 'وحدة' }}
                                                </span>
                                            </td>
                                            <td class="text-center">

                                                <div class="no-print">
                                                    <input type="number" class="quantity-input"
                                                        name="actual_quantity_{{ $data['item']->id }}"
                                                        value="{{ $data['actual_quantity'] }}" step="0.01"
                                                        onchange="updateQuantity({{ $data['item']->id }}, this.value)">
                                                </div>

                                                <div class="d-print-block d-none">
                                                    <span>
                                                        {{ number_format($data['actual_quantity'], 2) }}
                                                        {{ $data['main_unit']->name ?? 'وحدة' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center discrepancy">
                                                <span
                                                    class="fw-bold {{ $data['discrepancy'] < 0 ? 'text-danger' : ($data['discrepancy'] > 0 ? 'text-primary' : 'text-success') }}">
                                                    {{ $data['discrepancy'] > 0 ? '+' : '' }}{{ number_format($data['discrepancy'], 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center status">
                                                @if ($data['discrepancy'] < 0)
                                                    <span class="badge-shortage">{{ $data['discrepancy_type'] }}</span>
                                                @elseif($data['discrepancy'] > 0)
                                                    <span class="badge-overage">{{ $data['discrepancy_type'] }}</span>
                                                @else
                                                    <span class="badge-match">{{ $data['discrepancy_type'] }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="ri-inbox-line display-4 text-muted"></i>
                                                <p class="text-muted mt-2">لا توجد أصناف للعرض</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // إضافة رمز CSRF للطلبات
            const csrfToken = "{{ csrf_token() }}";

            // دالة لتحديث كمية صنف واحد
            function updateQuantity(itemId, quantity) {
                fetch("{{ route('inventory.update') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            item_id: itemId,
                            actual_quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // تحديث الصف في الجدول دون إعادة تحميل
                            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                            if (row) {
                                const discrepancyCell = row.querySelector('.discrepancy');
                                const statusCell = row.querySelector('.status');

                                discrepancyCell.textContent = (data.discrepancy > 0 ? '+' : '') + Number(data.discrepancy)
                                    .toFixed(2);
                                discrepancyCell.className = 'fw-bold text-center ' +
                                    (data.discrepancy < 0 ? 'text-danger' : (data.discrepancy > 0 ? 'text-primary' :
                                        'text-success'));

                                statusCell.innerHTML =
                                    `<span class="badge-${data.discrepancy_type.toLowerCase()}">${data.discrepancy_type}</span>`;
                                row.className = `discrepancy-${data.discrepancy_type.toLowerCase()}`;
                            }
                        } else {
                            alert('حدث خطأ أثناء تحديث الكمية: ' + (data.message || 'خطأ غير معروف'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء الاتصال بالخادم.');
                    });
            }

            // دالة لتحديث جميع الكميات
            function updateAllQuantities() {
                const inputs = document.querySelectorAll('.quantity-input');
                const quantities = {};

                inputs.forEach(input => {
                    const itemId = input.name.replace('actual_quantity_', '');
                    quantities[itemId] = input.value;
                });

                fetch("{{ route('inventory.updateAll') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            quantities: quantities
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // تحديث جميع الصفوف في الجدول
                            data.updatedItems.forEach(item => {
                                const row = document.querySelector(`tr[data-item-id="${item.item_id}"]`);
                                if (row) {
                                    const discrepancyCell = row.querySelector('.discrepancy');
                                    const statusCell = row.querySelector('.status');

                                    discrepancyCell.textContent = (item.discrepancy > 0 ? '+' : '') + Number(item
                                        .discrepancy).toFixed(2);
                                    discrepancyCell.className = 'fw-bold text-center ' +
                                        (item.discrepancy < 0 ? 'text-danger' : (item.discrepancy > 0 ?
                                            'text-primary' : 'text-success'));

                                    statusCell.innerHTML =
                                        `<span class="badge-${item.discrepancy_type.toLowerCase()}">${item.discrepancy_type}</span>`;
                                    row.className = `discrepancy-${item.discrepancy_type.toLowerCase()}`;
                                }
                            });
                        } else {
                            alert('حدث خطأ أثناء تحديث الكميات: ' + (data.message || 'خطأ غير معروف'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء الاتصال بالخادم.');
                    });
            }

            // إضافة تأثيرات بصرية للجدول
            document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    row.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.02)';
                        this.style.transition = 'transform 0.2s ease';
                    });

                    row.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            });
        </script>
    @endpush
@endsection
