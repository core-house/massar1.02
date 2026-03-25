{{-- مودال اختيار الطاولة (Dining) --}}
<div class="modal fade" id="orderTypeModal" tabindex="-1" aria-labelledby="orderTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderTypeModalLabel">
                    <i class="fas fa-chair text-primary me-2"></i>
                    {{ __('pos.select_table') ?? 'اختيار الطاولة' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Legend --}}
                <div class="rpos-modal-legend">
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
                        <div class="text-center text-muted py-4 w-100">
                            <i class="fas fa-chair fa-2x mb-2 d-block opacity-25"></i>
                            {{ __('pos.no_tables') ?? 'لا توجد طاولات' }}
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pos.cancel') ?? 'إلغاء' }}</button>
                <button type="button" class="btn btn-primary" id="confirmTableBtn" disabled>
                    <i class="fas fa-check me-1"></i> {{ __('pos.confirm_table') ?? 'تأكيد الطاولة' }}
                </button>
            </div>
        </div>
    </div>
</div>
