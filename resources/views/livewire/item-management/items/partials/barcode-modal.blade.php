<!-- Barcode Modal (Bootstrap) -->
<div wire:ignore.self class="modal fade" id="barcodeModal" tabindex="-1" role="dialog" aria-labelledby="barcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-hold fw-bold" id="barcodeModalLabel">{{ __('items.manage_additional_barcodes') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="font-hold fw-bold mb-0">{{ __('items.additional_barcodes') }}</h6>
                            <button type="button" class="btn btn-success btn-sm font-hold fw-bold"
                                wire:click.prevent="addModalBarcode()">
                                <i class="las la-plus me-1"></i> {{ __('items.add_barcode') }}
                            </button>
                        </div>
                        
                        @if(count($modalBarcodeData) > 0)
                            @foreach($modalBarcodeData as $index => $barcode)
                                <div class="input-group mb-2" wire:key="additional-barcode-{{ $index }}">
                                    <span class="input-group-text bg-light">
                                        <i class="las la-barcode text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           wire:model="modalBarcodeData.{{ $index }}"
                                           id="modalBarcodeInput.{{ $index }}"
                                           class="form-control font-hold fw-bold" 
                                           placeholder="{{ __('items.enter_barcode') }}"
                                           maxlength="25">
                                    <button type="button" 
                                            class="btn btn-outline-danger"
                                            wire:click="removeModalBarcode({{ $index }})"
                                            title="{{ __('items.delete_barcode') }}">
                                        <i class="las la-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="las la-barcode text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">{{ __('items.no_additional_barcodes') }}</p>
                            </div>
                        @endif
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary font-hold fw-bold" data-bs-dismiss="modal">
                    {{ __('common.cancel') }}
                </button>
                <button type="button" class="btn btn-main font-hold fw-bold" 
                        wire:click="saveAdditionalBarcodes">
                    <i class="las la-save me-1"></i> {{ __('items.save_barcodes') }}
                </button>
            </div>
        </div>
    </div>
</div>


