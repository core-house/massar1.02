@props(['model', 'permission' => 'delete Inquiries'])

<div x-data="{
    selectedIds: [],
    allIds: [],
    selectAll: false,

    init() {
        this.updateAllIds();
        this.$watch('selectedIds', value => {
            this.selectAll = (value.length === this.allIds.length && this.allIds.length > 0);
        });
    },

    updateAllIds() {
        this.allIds = Array.from(this.$root.querySelectorAll('.bulk-checkbox')).map(el => el.value);
    },

    toggleAll() {
        if (this.selectAll) {
            this.selectedIds = [...this.allIds];
        } else {
            this.selectedIds = [];
        }
    },

    async performBulkAction(action) {
        if (this.selectedIds.length === 0) return;

        const confirmMessage = action === 'delete'
            ? '{{ __("Are you sure you want to delete the selected items?") }}'
            : '{{ __("Are you sure?") }}';

        if (!confirm(confirmMessage)) return;

        try {
            const response = await fetch('{{ route("inquiries.bulk-actions") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ids: this.selectedIds,
                    model: @js($model),
                    action: action
                })
            });

            const result = await response.json();

            if (result.success) {
                window.location.reload();
            } else {
                alert(result.message || '{{ __("An error occurred") }}');
            }
        } catch (error) {
            console.error('Bulk action error:', error);
            alert('{{ __("An error occurred while processing the request") }}');
        }
    }
}" @content-changed.window="updateAllIds()" class="position-relative">

    {{-- Minimal Pill Bulk Action Bar --}}
    <template x-if="selectedIds.length > 0">
        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="bulk-pill d-flex align-items-center bg-white border shadow-sm rounded-pill px-3 py-1 mb-0 mx-auto position-absolute start-50 translate-middle-x"
             style="width: fit-content; border: 1px solid #dee2e6 !important; z-index: 100; top: -35px;">

            <div class="d-flex align-items-center border-end pe-3 me-3">
                <span class="badge bg-primary rounded-pill me-2" x-text="selectedIds.length"></span>
                <span class="text-dark fw-bold small text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">{{ __('Selected') }}</span>
            </div>

            <div class="d-flex gap-1 align-items-center">
                <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none p-0 px-2" @click="selectedIds = []; selectAll = false;">
                    <i class="fas fa-times me-1"></i> <span class="small">{{ __('Cancel') }}</span>
                </button>

                @can($permission)
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-0" style="height: 24px; font-size: 0.75rem;" @click="performBulkAction('delete')">
                    <i class="fas fa-trash-alt me-1"></i> {{ __('Delete') }}
                </button>
                @endcan
            </div>
        </div>
    </template>

    {{-- The Table Wrapper --}}
    <div class="bulk-table-wrapper">
        {{ $slot }}
    </div>

</div>

<style>
    .bulk-checkbox {
        width: 17px;
        height: 17px;
        cursor: pointer;
        border: 2px solid #dee2e6;
        transition: all 0.2s;
    }
    .bulk-checkbox:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }
    .bulk-pill {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-color: #dee2e6 !important;
    }
</style>
