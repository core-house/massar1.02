@if($showModal)
<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title font-hold fw-bold text-white" id="universalModalLabel">
                    {{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('livewire.item-management.items.partials.alerts')
                <form wire:submit.prevent="saveModalData">
                    <div class="mb-3">
                        <label for="modalName" class="form-label font-hold fw-bold">{{ __('common.name') }}</label>
                        <input type="text" wire:model="modalData.name" class="form-control font-hold fw-bold" id="modalName" placeholder="{{ __('common.enter_name') }}" autofocus>
                        @error('modalData.name')
                            <span class="text-danger font-hold fw-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary font-hold fw-bold" wire:click="closeModal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-main font-hold fw-bold" wire:click="saveModalData" wire:loading.attr="disabled" wire:target="saveModalData">
                    <span wire:loading.remove wire:target="saveModalData">{{ __('common.save') }}</span>
                    <span wire:loading wire:target="saveModalData">{{ __('common.saving') }}...</span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif


