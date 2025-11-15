<div class="container-fluid" id="printable-invoice">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            تفاصيل فاتورة التصنيع
                        </h4>
                        <div class="btn-group no-print">
                            <a href="{{ route('manufacturing.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> رجوع
                            </a>
                            <a href="{{ route('manufacturing.edit', $invoice->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> تعديل
                            </a>
                            <button onclick="window.print()" class="btn btn-info btn-sm">
                                <i class="fas fa-print me-1"></i> طباعة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الفاتورة الأساسية -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات الفاتورة</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">رقم الفاتورة:</td>
                            <td><strong class="text-primary">{{ $invoice->pro_id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">التاريخ:</td>
                            <td><strong>{{ $invoice->pro_date }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">الوصف:</td>
                            <td>{{ $invoice->info ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">رقم الباتش:</td>
                            <td>{{ $invoice->patch_number ?: '-' }}</td>
                        </tr>
                        @if ($invoice->expected_time)
                            <tr>
                                <td class="text-muted">الوقت المتوقع:</td>
                                <td>{{ $invoice->expected_time }} ساعة</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>معلومات إضافية</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">الموظف:</td>
                            <td><strong>{{ $invoice->employee->aname ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">الفرع:</td>
                            <td>{{ $invoice->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">حساب المنتجات:</td>
                            <td>{{ $invoice->acc1Head->aname ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">حساب الخامات:</td>
                            <td>{{ $invoice->acc2Head->aname ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">الحساب التشغيلي:</td>
                            <td>{{ $invoice->store->aname ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- المنتجات المصنعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        المنتجات المصنعة
                    </h6>
                </div>
                <div class="card-body">
                    @if (count($products) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="35%">اسم المنتج</th>
                                        <th width="15%">الكمية</th>
                                        <th width="15%">تكلفة الوحدة</th>
                                        <th width="15%">نسبة التكلفة %</th>
                                        <th width="15%">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $index => $product)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $product['name'] }}</td>
                                            <td class="text-center">{{ number_format($product['quantity'], 2) }}</td>
                                            <td class="text-end">{{ number_format($product['unit_cost'], 2) }} ج</td>
                                            <td class="text-center">
                                                {{ number_format($product['cost_percentage'], 2) }}%</td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-success">{{ number_format($product['total_cost'], 2) }}
                                                    ج</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>الإجمالي:</strong></td>
                                        <td class="text-end">
                                            <strong
                                                class="text-success fs-5">{{ number_format($totals['products'], 2) }}
                                                ج</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>لا توجد منتجات مصنعة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- المواد الخام -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cubes me-2"></i>
                        المواد الخام
                    </h6>
                </div>
                <div class="card-body">
                    @if (count($rawMaterials) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="35%">اسم المادة</th>
                                        <th width="15%">الكمية</th>
                                        <th width="15%">الوحدة</th>
                                        <th width="15%">سعر التكلفة</th>
                                        <th width="15%">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rawMaterials as $index => $material)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $material['name'] }}</td>
                                            <td class="text-center">{{ number_format($material['quantity'], 2) }}</td>
                                            <td class="text-center">{{ $material['unit_name'] }}</td>
                                            <td class="text-end">{{ number_format($material['unit_cost'], 2) }} ج</td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-info">{{ number_format($material['total_cost'], 2) }}
                                                    ج</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>الإجمالي:</strong></td>
                                        <td class="text-end">
                                            <strong
                                                class="text-info fs-5">{{ number_format($totals['raw_materials'], 2) }}
                                                ج</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-cubes fa-3x mb-3"></i>
                            <p>لا توجد مواد خام</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- المصروفات الإضافية -->
    @if (count($expenses) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            المصروفات الإضافية
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="40%">الوصف</th>
                                        <th width="35%">الحساب</th>
                                        <th width="20%">المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $index => $expense)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $expense['description'] }}</td>
                                            <td>{{ $expense['account_name'] }}</td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-warning">{{ number_format($expense['amount'], 2) }}
                                                    ج</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>الإجمالي:</strong></td>
                                        <td class="text-end">
                                            <strong
                                                class="text-warning fs-5">{{ number_format($totals['expenses'], 2) }}
                                                ج</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ملخص التكاليف -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-6">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        ملخص التكاليف
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">تكلفة المواد الخام:</td>
                            <td class="text-end">
                                <strong class="text-info">{{ number_format($totals['raw_materials'], 2) }} ج</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">المصروفات الإضافية:</td>
                            <td class="text-end">
                                <strong class="text-warning">{{ number_format($totals['expenses'], 2) }} ج</strong>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted"><strong>إجمالي تكلفة التصنيع:</strong></td>
                            <td class="text-end">
                                <strong class="text-danger fs-5">{{ number_format($totals['manufacturing_cost'], 2) }}
                                    ج</strong>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted"><strong>قيمة المنتجات المصنعة:</strong></td>
                            <td class="text-end">
                                <strong class="text-success fs-5">{{ number_format($totals['products'], 2) }}
                                    ج</strong>
                            </td>
                        </tr>
                        <tr class="border-top bg-light">
                            <td><strong>الفرق:</strong></td>
                            <td class="text-end">
                                @php
                                    $difference = $totals['products'] - $totals['manufacturing_cost'];
                                    $color = $difference >= 0 ? 'success' : 'danger';
                                @endphp
                                <strong class="text-{{ $color }} fs-4">{{ number_format($difference, 2) }}
                                    ج</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }

            .card-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                font-size: 12px;
            }
        }
    </style>
@endpush
