<div>
    {{-- Loading Indicator --}}
    <div wire:loading class="loading-overlay">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="ri-filter-3-line"></i> فلاتر التقرير</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">المخزن</label>
                    <select wire:model.live="selectedWarehouse" class="form-select">
                        <option value="">اختر المخزن</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->aname }}</option>
                        @endforeach
                    </select>
                    @error('selectedWarehouse')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>


                <div class="col-md-4">
                    <label class="form-label">حساب تسوية الجرد</label>
                    <select wire:model="selectedPartner" class="form-select">
                        @foreach ($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->aname }}</option>
                        @endforeach
                    </select>
                    @error('selectedPartner')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary w-100" wire:click="applyInventoryAdjustments"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="applyInventoryAdjustments"><i
                                    class="ri-refresh-line"></i> تحديث الأرصدة وتطبيق التسوية</span>
                            <span wire:loading wire:target="applyInventoryAdjustments">جاري التطبيق...</span>
                        </button>
                        <button type="button" class="btn btn-success" onclick="window.print()"> طباعه</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات سريعة --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">إجمالي الأصناف</h6>
                    <h4 class="text-primary">{{ $totalItems }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">أصناف بها زيادة</h6>
                    <h4 class="text-info">{{ $itemsWithOverage }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">أصناف بها نقص</h6>
                    <h4 class="text-danger">{{ $itemsWithShortage }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">أصناف مطابقة</h6>
                    <h4 class="text-success">{{ $itemsMatching }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Container --}}
    <div id="alertContainer"></div>

    {{-- إشعار حساب الفروقات --}}
    @if (!$inventoryDifferenceAccount)
        <div class="alert alert-warning">
            <i class="ri-alert-line"></i>
            تحذير: حساب فروقات الجرد غير محدد في الإعدادات العامة. يرجى تحديد قيمة للمفتاح
            <code>show_inventory_difference_account</code> في جدول PublicSettings.
        </div>
    @endif

    {{-- Data Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>اسم الصنف</th>
                            <th>التكلفة</th>
                            <th>الرصيد الدفتري</th>
                            <th>الكمية الفعلية (الجرد)</th>
                            <th>الفرق (الكمية)</th>
                            <th>قيمة الفرق</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryData as $index => $data)
                            <tr
                                class="{{ $data['discrepancy'] < 0 ? 'table-danger' : ($data['discrepancy'] > 0 ? 'table-info' : '') }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    {{ $data['item']->name }}
                                    <br><small class="text-muted">{{ $data['item']->code }}</small>
                                </td>
                                <td class="text-center">
                                    {{ number_format($data['item']->cost ?? 0, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($data['system_quantity'], 2) }}
                                    <br><small>{{ $data['main_unit']?->name }}</small>
                                </td>
                                <td style="width: 150px;">
                                    <input type="number"
                                        wire:model.live.debounce.500ms="quantities.{{ $data['item']->id }}"
                                        class="form-control form-control-sm text-center" step="any">
                                </td>
                                <td class="text-center fw-bold">
                                    <span
                                        class="{{ $data['discrepancy'] < 0 ? 'text-danger' : ($data['discrepancy'] > 0 ? 'text-primary' : 'text-success') }}">
                                        {{ $data['discrepancy'] > 0 ? '+' : '' }}{{ number_format($data['discrepancy'], 2) }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold">
                                    <span
                                        class="{{ $data['discrepancy_value'] < 0 ? 'text-danger' : ($data['discrepancy_value'] > 0 ? 'text-primary' : 'text-success') }}">
                                        {{ $data['discrepancy_value'] > 0 ? '+' : '' }}{{ number_format($data['discrepancy_value'] ?? $data['discrepancy'] * ($data['item']->cost ?? 0), 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    @if (!$selectedWarehouse)
                                        الرجاء اختيار مخزن لعرض الأصناف
                                    @else
                                        لا توجد أصناف لعرضها
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ملخص القيم --}}
            @if (count($inventoryData) > 0)
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6>إجمالي قيمة الزيادات</h6>
                                <h5>{{ number_format(collect($inventoryData)->where('discrepancy_value', '>', 0)->sum('discrepancy_value'), 2) }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h6>إجمالي قيمة النقص</h6>
                                <h5>{{ number_format(abs(collect($inventoryData)->where('discrepancy_value', '<', 0)->sum('discrepancy_value')), 2) }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h6>صافي الفرق</h6>
                                <h5>{{ number_format(collect($inventoryData)->sum('discrepancy_value'), 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('show-alert', (event) => {
                    const eventData = event[0];
                    const alertContainer = document.getElementById('alertContainer');
                    const alertClass = eventData.type === 'success' ? 'alert-success' :
                        eventData.type === 'error' ? 'alert-danger' :
                        eventData.type === 'info' ? 'alert-info' : 'alert-warning';

                    alertContainer.innerHTML = `
                        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                            ${eventData.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                    // إخفاء التنبيه تلقائياً بعد 5 ثوان
                    setTimeout(() => {
                        const alert = alertContainer.querySelector('.alert');
                        if (alert) {
                            alert.remove();
                        }
                    }, 5000);
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, .7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .table th,
            .table td {
                vertical-align: middle;
            }

            .card {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border: 1px solid rgba(0, 0, 0, 0.125);
            }

            @media print {

                .btn,
                .loading-overlay,
                #alertContainer {
                    display: none !important;
                }
            }
        </style>
    @endpush
</div>
