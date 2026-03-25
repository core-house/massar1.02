@props(['type', 'currentTypeInfo'])

@php
    $availableTypes = \App\Helpers\VoucherHelper::getAvailableTypes();
    $hasAnyPermission = !empty($availableTypes);
@endphp

@if($hasAnyPermission)
    @if (isset($currentTypeInfo['show_dropdown']) && $currentTypeInfo['show_dropdown'])
        {{-- Dropdown for multiple types --}}
        <div class="dropdown">
            <button class="btn btn-main dropdown-toggle" type="button" id="addVoucherDropdown" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-plus me-2"></i>
                {{ __('Add New Voucher') }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="addVoucherDropdown">
                @php
                    $regularTypes = array_intersect_key($availableTypes, array_flip(['receipt', 'payment', 'exp-payment']));
                    $multiTypes = array_intersect_key($availableTypes, array_flip(['multi_payment', 'multi_receipt']));
                @endphp

                @foreach($regularTypes as $typeKey => $typeConfig)
                    <li>
                        <a class="dropdown-item" href="{{ route($typeConfig['route'], ['type' => $typeKey]) }}">
                            <i class="fas {{ $typeConfig['icon'] }} text-{{ $typeConfig['color'] }} me-2"></i>
                            {{ $typeConfig['label'] }}
                        </a>
                    </li>
                @endforeach

                @if(!empty($regularTypes) && !empty($multiTypes))
                    <li><hr class="dropdown-divider"></li>
                @endif

                @foreach($multiTypes as $typeKey => $typeConfig)
                    <li>
                        <a class="dropdown-item" href="{{ route($typeConfig['route'], ['type' => $typeKey]) }}">
                            <i class="fas {{ $typeConfig['icon'] }} text-{{ $typeConfig['color'] }} me-2"></i>
                            {{ $typeConfig['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        {{-- Single button for specific type --}}
        @if(isset($availableTypes[$type]))
            @php $typeConfig = $availableTypes[$type]; @endphp
            <a href="{{ route($typeConfig['route'], ['type' => $type]) }}" 
               class="btn btn-{{ $currentTypeInfo['color'] }}">
                <i class="fas {{ $currentTypeInfo['icon'] }} me-2"></i>
                {{ $currentTypeInfo['create_text'] }}
            </a>
        @endif
    @endif
@endif
