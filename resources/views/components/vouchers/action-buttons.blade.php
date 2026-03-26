@props(['voucher'])

@php
    $canEdit = \App\Helpers\VoucherHelper::canPerformAction($voucher, 'edit');
    $canDelete = \App\Helpers\VoucherHelper::canPerformAction($voucher, 'delete');
    $canDuplicate = in_array($voucher->pro_type, [32, 33]) && 
                    \App\Helpers\VoucherHelper::canPerformAction($voucher, 'create');
    $isMultiVoucher = in_array($voucher->pro_type, [32, 33]);
@endphp

<div class="btn-group" role="group">
    {{-- View Journal Entry --}}
    <button type="button" 
            class="btn btn-sm btn-primary" 
            data-bs-toggle="modal" 
            data-bs-target="#journalEntryModal{{ $voucher->id }}"
            title="{{ __('View Journal Entry') }}">
        <i class="fas fa-book"></i>
    </button>

    {{-- Edit Button --}}
    @if($canEdit)
        <a href="{{ route($isMultiVoucher ? 'multi-vouchers.edit' : 'vouchers.edit', $voucher) }}" 
           class="btn btn-sm btn-warning" 
           title="{{ __('Edit') }}">
            <i class="fas fa-edit"></i>
        </a>
    @endif

    {{-- Duplicate Button (Multi Vouchers Only) --}}
    @if($canDuplicate)
        <a href="{{ route('multi-vouchers.duplicate', $voucher) }}" 
           class="btn btn-sm btn-info" 
           title="{{ __('Duplicate Operation') }}">
            <i class="fas fa-copy"></i>
        </a>
    @endif

    {{-- Delete Button --}}
    @if($canDelete)
        <form action="{{ route($isMultiVoucher ? 'multi-vouchers.destroy' : 'vouchers.destroy', $voucher->id) }}" 
              method="POST" 
              style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger"
                    onclick="return confirm('{{ __('Are you sure you want to delete this voucher?') }}')" 
                    title="{{ __('Delete') }}">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endif
</div>
