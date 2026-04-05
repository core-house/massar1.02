@php
    $hasChildren = $account->relationLoaded('children') && $account->children->isNotEmpty();
    $isBasic = $account->is_basic == 1;
    $balanceValue = (float) ($account->balance ?? 0);
    $section = $section ?? null;
    
    // Determine section styling
    $sectionColor = match($section) {
        'assets' => 'primary',
        'liabilities' => 'info',
        'equity' => 'success',
        default => 'primary'
    };
@endphp

<tr class="account-row level-{{ $level }} {{ $isBasic ? 'account-basic border-start border-4 border-'.$sectionColor : 'account-sub' }} {{ $level > 0 ? 'd-none children-' . ($parentId ?? '') : '' }} transition-all"
    data-level="{{ $level }}" 
    data-account-id="{{ $account->id }}"
    data-parent-id="{{ $parentId ?? '' }}" 
    data-has-children="{{ $hasChildren ? 1 : 0 }}"
    data-balance="{{ $balanceValue }}"
    @if ($section) data-section="{{ $section }}" @endif>
    
    <td class="align-middle ps-4 py-3 border-0">
        <div class="d-flex align-items-center" style="margin-left: {{ $level * 25 }}px;">
            @if ($hasChildren)
                <button type="button" 
                    class="btn btn-sm btn-link p-0 me-3 text-decoration-none collapse-toggle collapsed transition-all" 
                    data-account-id="{{ $account->id }}" 
                    aria-expanded="false"
                    onclick="toggleChildren(this)">
                    <i class="las la-plus-square fs-5 text-muted toggle-icon"></i>
                </button>
            @else
                <div class="me-3 opacity-25" style="width: 20px; border-top: 1px solid #dee2e6; margin-top: 2px;"></div>
            @endif
            
            <div class="d-flex align-items-center flex-grow-1">
                <div class="bg-soft-{{ $sectionColor }} p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="las {{ $hasChildren ? 'la-folder-open' : 'la-file-alt' }} text-{{ $sectionColor }} {{ $isBasic ? 'fs-4' : 'fs-5' }}"></i>
                </div>
                
                <div class="d-flex flex-column">
                    <span class="{{ $isBasic ? 'fw-bold text-dark fs-6' : 'fw-semibold text-muted small' }} transition-all name-label">
                        {{ $account->aname }}
                    </span>
                    <span class="smallest text-muted text-uppercase ls-1" style="font-size: 10px;">{{ $account->code }}</span>
                </div>
                
                @if ($isBasic && $level == 0)
                    <span class="ms-auto badge bg-soft-{{ $sectionColor }} text-{{ $sectionColor }} rounded-pill px-3 py-1 small fw-bold ls-1" style="font-size: 0.65rem;">
                        {{ __('reports::reports.primary') }}
                    </span>
                @endif
            </div>
        </div>
    </td>
    
    <td class="text-end align-middle pe-4 border-0 py-3">
        <span class="{{ $isBasic ? 'fw-bold text-dark fs-6' : 'fw-medium text-dark small' }} amount-value">
            {{ number_format($balanceValue, 2) }}
        </span>
    </td>
</tr>

@if ($hasChildren)
    @foreach ($account->children as $child)
        @include('reports::accounts-reports.partials.account-row-recursive', [
            'account' => $child,
            'level' => $level + 1,
            'parentId' => $account->id,
            'section' => $section,
        ])
    @endforeach
@endif


