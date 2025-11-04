<li class="nav-item">
    <a class="nav-link" href="{{ route('clients.index') }}">
        <i class="ti-control-record"></i>{{ __('Central Data') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('inquiries.index') }}">
        <i class="ti-control-record"></i>{{ __('Inquiries') }}
    </a>
</li>

<li>
    <a class="dropdown-item" href="{{ route('inquiries.drafts') }}">
        <i class="fas fa-file-alt me-2"></i>
        {{ __('My Drafts') }}
        @php
            $draftCount = \Modules\Inquiries\Models\Inquiry::myDrafts()->count();
        @endphp
        @if ($draftCount > 0)
            <span class="badge bg-warning text-dark ms-2">{{ $draftCount }}</span>
        @endif
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('inquiry.sources.index') }}">
        <i class="ti-control-record"></i>{{ __('Inquiries Source') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('work.types.index') }}">
        <i class="ti-control-record"></i>{{ __('Work Types') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('difficulty-matrix.create') }}">
        <i class="ti-control-record"></i>{{ __('Diffculty Matrix') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('quotation-info.create') }}">
        <i class="ti-control-record"></i>{{ __('Quotation Info') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('inquiry.documents.index') }}">
        <i class="ti-control-record"></i>{{ __('Documents') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('project-size.index') }}">
        <i class="ti-control-record"></i>{{ __('Project Size') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('inquiries-roles.index') }}">
        <i class="ti-control-record"></i>{{ __('Inquiries Roles') }}
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('inquiries.dashboard.statistics') }}">
        <i class="ti-bar-chart"></i>{{ __('Inquiries Statistics') }}
    </a>
</li>
