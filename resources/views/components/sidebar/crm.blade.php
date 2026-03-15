@can('view Inquiries Statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('statistics.index') }}">
            <i class="las la-chart-pie"></i>{{ __('crm::crm.statistics') }}
        </a>
    </li>
@endcan

@can('view Clients')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('clients.index') }}">
            <i class="las la-users"></i>{{ __('crm::crm.clients') }}
        </a>
    </li>
@endcan

@can('view Chance Sources')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('chance-sources.index') }}">
            <i class="las la-bullseye"></i>{{ __('crm::crm.chance_source') }}
        </a>
    </li>
@endcan

@can('view Client Contacts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('client-contacts.index') }}">
            <i class="las la-address-book"></i>{{ __('crm::crm.client_contacts') }}
        </a>
    </li>
@endcan

@can('view Client Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('client-types.index') }}">
            <i class="las la-user-tag"></i>{{ __('crm::crm.clients_types') }}
        </a>
    </li>
@endcan

@can('view Client Categories')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('client.categories.index') }}">
            <i class="las la-tags"></i>{{ __('crm::crm.clients_categories') }}
        </a>
    </li>
@endcan

@can('view Lead Statuses')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('lead-status.index') }}">
            <i class="las la-flag"></i>{{ __('crm::crm.lead_status') }}
        </a>
    </li>
@endcan

@can('view Leads')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('leads.board') }}">
            <i class="las la-user-plus"></i>{{ __('crm::crm.lead') }}
        </a>
    </li>
@endcan

@can('view Tickets')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('tickets.index') }}">
            <i class="las la-ticket-alt"></i>{{ __('crm::crm.tickets') }}
        </a>
    </li>
@endcan

@can('view Returns')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('returns.index') }}">
            <i class="las la-undo"></i>{{ __('crm::crm.returns') }}
        </a>
    </li>
@endcan

@can('view Campaigns')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('campaigns.index') }}">
            <i class="las la-bullhorn"></i>{{ __('crm::crm.marketing_campaigns') }}
        </a>
    </li>
@endcan



{{-- @can('view Activities')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('activities.index') }}">
            <i class="ti-control-record"></i>{{ __('crm::crm.activities') }}
        </a>
    </li>
@endcan --}}
