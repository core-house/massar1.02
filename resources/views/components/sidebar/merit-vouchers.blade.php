@can('view salary-calculation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'salary_calculation']) }}">
            <i class="las la-money-bill-wave"></i>{{ __('navigation.fixed_salary_calculation') }}
        </a>
    </li>
@endcan

@can('view extra-salary-calculation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'extra_calc']) }}">
            <i class="las la-plus-circle"></i>{{ __('navigation.extra_salary_calculation') }}
        </a>
    </li>
@endcan

@can('view discount-salary-calculation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'discount_calc']) }}">
            <i class="las la-minus-circle"></i>{{ __('navigation.discount_salary_calculation') }}
        </a>
    </li>
@endcan

@can('view insurance-calculation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'insurance_calc']) }}">
            <i class="las la-shield-alt"></i>{{ __('navigation.insurance_calculation') }}
        </a>
    </li>
@endcan

@can('view tax-calculation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'tax_calc']) }}">
            <i class="las la-percentage"></i>{{ __('navigation.tax_calculation') }}
        </a>
    </li>
@endcan
