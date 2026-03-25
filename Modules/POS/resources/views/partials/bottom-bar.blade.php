{{-- Bottom Action Bar --}}
<div class="pos-bottom-bar bg-white shadow-lg" style="padding: 1rem; border-top: 1px solid #e0e0e0;">
    <div class="d-flex align-items-center justify-content-end gap-3">
        <button type="button" 
                id="customerBtn"
                class="btn btn-outline-primary"
                style="border-radius: 25px; padding: 0.75rem 1.5rem;">
            <i class="fas fa-user me-2"></i> {{ __('pos.customer') }}
        </button>
        <button type="button" 
                id="paymentBtn"
                class="btn btn-success"
                style="border-radius: 25px; padding: 0.75rem 1.5rem;">
            <i class="fas fa-money-bill-wave me-2"></i> {{ __('pos.payment') }}
        </button>
        <button type="button" 
                id="notesBtn"
                class="btn btn-outline-secondary"
                style="border-radius: 25px; padding: 0.75rem 1.5rem;">
            <i class="fas fa-sticky-note me-2"></i> {{ __('pos.notes') }}
        </button>
        <button type="button" 
                id="returnInvoiceBtn"
                class="btn btn-outline-warning"
                style="border-radius: 25px; padding: 0.75rem 1.5rem;"
                title="{{ __('pos.return_invoice_btn') }}">
            <i class="fas fa-undo me-2"></i> {{ __('pos.return_invoice_btn') }}
        </button>
        <button type="button" 
                id="payOutBtn"
                class="btn btn-outline-danger"
                style="border-radius: 25px; padding: 0.75rem 1.5rem;"
                title="{{ __('pos.pay_out_btn') }}">
            <i class="fas fa-money-bill-wave me-2"></i> {{ __('pos.pay_out_btn') }}
        </button>
        <div class="dropdown">
            <button class="btn btn-outline-secondary" 
                    type="button" 
                    id="moreOptionsDropdown" 
                    data-bs-toggle="dropdown"
                    style="border-radius: 25px; padding: 0.75rem 1.5rem;">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreOptionsDropdown">
                <li><a class="dropdown-item" href="#" id="tableBtn"><i class="fas fa-table me-2"></i> {{ __('pos.select_table') }}</a></li>
                <li><a class="dropdown-item" href="#" id="resetBtn"><i class="fas fa-redo me-2"></i> {{ __('pos.reset') }}</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('pos.index') }}"><i class="fas fa-home me-2"></i> {{ __('pos.back_to_home') }}</a></li>
            </ul>
        </div>
    </div>
</div>
