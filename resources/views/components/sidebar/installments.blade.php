@can('view Installment Plans')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('installments.plans.index') }}">
            <i class="las la-list-alt"></i>{{ trans_str('view installment plans') }}
        </a>
    </li>
@endcan

@can('create Installment Plans')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('installments.plans.create') }}">
            <i class="las la-plus-circle"></i>{{ trans_str('add new plan') }}
        </a>
    </li>
@endcan

@can('view Overdue Installments')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('installments.payments.overdue') }}">
            <i class="las la-exclamation-triangle"></i>{{ trans_str('overdue installments') }}
        </a>
    </li>
@endcan
