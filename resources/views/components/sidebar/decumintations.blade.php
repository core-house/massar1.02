<li class="menu-title mt-2">{{ __('decumintations.documents') }}</li>

@can('view Documents')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('documents.index') ? 'active' : '' }}"
           href="{{ route('documents.index') }}"
           style="{{ request()->routeIs('documents.index') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-file-alt font-18"></i>{{ __('decumintations.documents') }}
        </a>
    </li>
@endcan

@can('create Documents')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('documents.create') ? 'active' : '' }}"
           href="{{ route('documents.create') }}"
           style="{{ request()->routeIs('documents.create') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-plus-circle font-18"></i>{{ __('decumintations.add_document') }}
        </a>
    </li>
@endcan

@can('view Document Categories')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('document-categories.index') ? 'active' : '' }}"
           href="{{ route('document-categories.index') }}"
           style="{{ request()->routeIs('document-categories.index') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-folder font-18"></i>{{ __('decumintations.document_categories') }}
        </a>
    </li>
@endcan

@can('create Document Categories')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('document-categories.create') ? 'active' : '' }}"
           href="{{ route('document-categories.create') }}"
           style="{{ request()->routeIs('document-categories.create') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-folder-plus font-18"></i>{{ __('decumintations.add_category') }}
        </a>
    </li>
@endcan
