@can('view Inquiries')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiries.index') }}">
            <i class="las la-question-circle"></i>{{ __('inquiries') }}
        </a>
    </li>
@endcan

@can('view Contacts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('contacts.index') }}">
            <i class="las la-address-book"></i>{{ __('contacts') }}
        </a>
    </li>
@endcan

@can('view My Drafts')
    <li>
        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('inquiries.drafts') }}">
            <i class="las la-file-alt"></i>
            {{ __('my drafts') }}
        </a>
    </li>
@endcan

@can('view Inquiries Source')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiry.sources.index') }}">
            <i class="las la-bullseye"></i>{{ __('inquiries source') }}
        </a>
    </li>
@endcan

@can('view Work Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('work.types.index') }}">
            <i class="las la-briefcase"></i>{{ __('work types') }}
        </a>
    </li>
@endcan

@can('view Difficulty Matrix')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('difficulty-matrix.create') }}">
            <i class="las la-th"></i>{{ __('difficulty matrix') }}
        </a>
    </li>
@endcan

@can('view Quotation Info')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('quotation-info.create') }}">
            <i class="las la-file-invoice"></i>{{ __('quotation info') }}
        </a>
    </li>
@endcan

@can('view Documents')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiry.documents.index') }}">
            <i class="las la-folder-open"></i>{{ __('documents') }}
        </a>
    </li>
@endcan

@can('view Project Size')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('project-size.index') }}">
            <i class="las la-ruler-combined"></i>{{ __('project size') }}
        </a>
    </li>
@endcan

@can('view Inquiries Roles')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiries-roles.index') }}">
            <i class="las la-user-tag"></i>{{ __('inquiries roles') }}
        </a>
    </li>
@endcan

@can('view Pricing Statuses')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('pricing-statuses.index') }}">
            <i class="las la-flag"></i>{{ __('pricing status') }}
        </a>
    </li>
@endcan

@can('view Inquiries Statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiries.dashboard.statistics') }}">
            <i class="las la-chart-bar"></i>{{ __('inquiries statistics') }}
        </a>
    </li>
@endcan
