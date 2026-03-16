@props(['field', 'sortField', 'sortDirection', 'label'])

@php
    $isActive = $sortField === $field;
    $newDirection = $isActive && $sortDirection === 'asc' ? 'desc' : 'asc';
    $currentUrl = request()->fullUrlWithQuery(['sort' => $field, 'direction' => $newDirection]);
@endphp

<th class="cursor-pointer hover:bg-gray-50" style="cursor: pointer;">
    <a href="{{ $currentUrl }}" class="d-flex align-items-center justify-content-center text-decoration-none text-dark">
        <span>{{ $label }}</span>
        <span class="ms-1">
            @if($isActive)
                @if($sortDirection === 'asc')
                    <i class="las la-sort-up text-primary"></i>
                @else
                    <i class="las la-sort-down text-primary"></i>
                @endif
            @else
                <i class="las la-sort text-muted" style="opacity: 0.3;"></i>
            @endif
        </span>
    </a>
</th>
