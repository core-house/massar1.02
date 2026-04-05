{{-- Table Selection Modal (Dining) --}}
<div class="modal fade" id="orderTypeModal" tabindex="-1" aria-labelledby="orderTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 40px rgba(0,0,0,.18);overflow:hidden;">

            <div class="modal-header py-3 px-4" style="background:var(--rpos-dark);border:none;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:var(--rpos-info);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-chair text-white" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0 text-white" id="orderTypeModalLabel">
                        {{ __('pos.select_table') ?? 'اختيار الطاولة' }}
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="background:#f8fafc;">
                {{-- Legend --}}
                <div class="rpos-modal-legend mb-3">
                    <span class="rpos-legend-item"><span class="rpos-legend-dot rpos-legend-dot--free"></span>{{ __('pos.table_free') ?? 'فارغة' }}</span>
                    <span class="rpos-legend-item"><span class="rpos-legend-dot rpos-legend-dot--occupied"></span>{{ __('pos.table_occupied') ?? 'مشغولة' }}</span>
                    <span class="rpos-legend-item"><span class="rpos-legend-dot rpos-legend-dot--reserved"></span>{{ __('pos.table_reserved') ?? 'محجوزة' }}</span>
                </div>

                {{-- Table Grid --}}
                <div class="rpos-modal-table-grid" id="rposModalTableGrid">
                    @forelse($restaurantTables ?? [] as $table)
                        @php
                            $status = $table->status ?? 'available';
                            $cssStatus = $status === 'available' ? 'free' : ($status === 'reserved' ? 'reserved' : 'occupied');
                        @endphp
                        <div class="rpos-table-card rpos-table-card--{{ $cssStatus }}"
                             data-table-id="{{ $table->id }}"
                             data-table-name="{{ $table->name }}"
                             data-table-status="{{ $cssStatus }}">
                            <span class="rpos-table-card__status-dot"></span>
                            <div class="rpos-table-card__icon"><i class="fas fa-chair"></i></div>
                            <div class="rpos-table-card__name">{{ $table->name }}</div>
                            <div class="rpos-table-card__cap"><i class="fas fa-user"></i> {{ $table->capacity ?? 4 }}</div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5 w-100">
                            <i class="fas fa-chair fa-2x mb-2 d-block opacity-25"></i>
                            <span class="small">{{ __('pos.no_tables') ?? 'لا توجد طاولات' }}</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="modal-footer py-2 px-4 gap-2" style="border-top:1px solid var(--rpos-border);background:#fff;">
                <button type="button" class="btn btn-sm btn-light fw-bold border" data-bs-dismiss="modal" style="border-radius:8px;">
                    {{ __('pos.cancel') ?? 'إلغاء' }}
                </button>
                <button type="button" class="btn btn-sm btn-primary fw-bold" id="confirmTableBtn" disabled style="border-radius:8px;">
                    <i class="fas fa-check me-1"></i>{{ __('pos.confirm_table') ?? 'تأكيد الطاولة' }}
                </button>
            </div>

        </div>
    </div>
</div>
