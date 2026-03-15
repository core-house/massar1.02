@can('view Inquiries')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiries.index') }}">
            <i class="las la-question-circle"></i>{{ trans_str('inquiries') }}
        </a>
    </li>
@endcan

@can('view Contacts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('contacts.index') }}">
            <i class="las la-address-book"></i>{{ trans_str('contacts') }}
        </a>
    </li>
@endcan

@can('view My Drafts')
    <li>
        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('inquiries.drafts') }}">
            <i class="las la-file-alt"></i>
            {{ trans_str('my drafts') }}
        </a>
    </li>
@endcan

@can('view Inquiries Source')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiry.sources.index') }}">
            <i class="las la-bullseye"></i>{{ trans_str('inquiries source') }}
        </a>
    </li>
@endcan

@can('view Work Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('work.types.index') }}">
            <i class="las la-briefcase"></i>{{ trans_str('work types') }}
        </a>
    </li>
@endcan

@can('view Difficulty Matrix')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('difficulty-matrix.create') }}">
            <i class="las la-th"></i>{{ trans_str('difficulty matrix') }}
        </a>
    </li>
@endcan

@can('view Quotation Info')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('quotation-info.create') }}">
            <i class="las la-file-invoice"></i>{{ trans_str('quotation info') }}
        </a>
    </li>
@endcan

@can('view Documents')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiry.documents.index') }}">
            <i class="las la-folder-open"></i>{{ trans_str('documents') }}
        </a>
    </li>
@endcan

@can('view Project Size')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('project-size.index') }}">
            <i class="las la-ruler-combined"></i>{{ trans_str('project size') }}
        </a>
    </li>
@endcan

@can('view Inquiries Roles')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiries-roles.index') }}">
            <i class="las la-user-tag"></i>{{ trans_str('inquiries roles') }}
        </a>
    </li>
@endcan

@can('view Pricing Statuses')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('pricing-statuses.index') }}">
            <i class="las la-flag"></i>{{ trans_str('pricing status') }}
        </a>
    </li>
@endcan

@can('view Inquiries Statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inquiries.dashboard.statistics') }}">
            <i class="las la-chart-bar"></i>{{ trans_str('inquiries statistics') }}
        </a>
    </li>
@endcan
