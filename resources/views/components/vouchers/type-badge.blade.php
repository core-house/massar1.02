@props(['proType', 'typeText' => null])

@php
    $badge = \App\Helpers\VoucherHelper::getTypeBadge($proType);
@endphp

<span class="badge {{ $badge['class'] }}">
    {{ $typeText ?? $badge['text'] }}
</span>
