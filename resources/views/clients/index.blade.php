@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Clients'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Clients')]],
    ])
    <style>
        .form-check-input.toggle-active {
            width: 2em;
            height: 1em;
        }

        .form-check-input.toggle-active:checked {
            background-color: #28a745;
        }

        span.d-inline-flex {
            vertical-align: middle;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            @can('create CRM Clients')
                <a href="{{ route('clients.create') }}" type="button" class="btn btn-main">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('crm::crm.add_new_client') }}
                </a>
            @endcan

            <br><br>
            @can('import CRM Clients')
                <x-app::excel-importer model="Client" :column-mapping="[
                    'cname' => 'cname',
                    'email' => 'email',
                    'phone' => 'phone',
                    'phone2' => 'phone2',
                    'address' => 'address',
                    'job' => 'job',
                    'gender' => 'gender',
                    'type' => 'type',
                    'national_id' => 'national_id',
                ]" :validation-rules="[]" button-text="{{ __('crm::crm.import_clients') }}"
                    button-size="small" />
            @endcan

            <form method="GET" action="{{ route('clients.index') }}" id="clients-filter-form" class="card mb-3">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <input type="text" name="search" id="search-input" class="form-control" placeholder="{{ __('crm::crm.search_by_name_phone_email') }}" value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <select name="client_type_id" class="form-control filter-select">
                                <option value="">{{ __('crm::crm.all_client_types') }}</option>
                                @foreach($clientTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('client_type_id') == $type->id ? 'selected' : '' }}>{{ $type->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="is_active" class="form-control filter-select">
                                <option value="">{{ __('crm::crm.all_client_statuses') }}</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ __('crm::crm.active') }}</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ __('crm::crm.inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-auto d-flex gap-1 align-items-center">
                            <button type="submit" class="btn btn-main btn-sm px-3"><i class="fas fa-search me-1"></i></button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm px-3"><i class="fas fa-times me-1"></i></a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="clients-table" filename="clients-table"
                            excel-label="{{ __('Export Excel') }}" pdf-label="{{ __('Export PDF') }}"
                            print-label="{{ __('Print') }}" />

                        <table id="clients-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Client Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Job') }}</th>
                                    <th>{{ __('Commercial Register') }}</th>
                                    <th>{{ __('Tax Certificate') }}</th>
                                    {{-- <th>{{ __('Date of Birth') }}</th> --}}
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Assigned User') }}</th>
                                    {{-- <th>{{ __('الجنس') }}</th> --}}
                                    <th>{{ __('Status') }}</th>
                                    @canany(['edit CRM Clients', 'delete CRM Clients'])
                                        <th>{{ __('Actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clients as $client)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $client->cname }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->phone }}</td>
                                        <td>{{ $client->address }}</td>
                                        <td>{{ $client->job }}</td>
                                        <td>{{ $client->commercial_register }}</td>
                                        <td>{{ $client->tax_certificate }}</td>
                                        {{-- <td>{{ $client->date_of_birth?->format('Y-m-d') }}</td> --}}
                                        <td>
                                            {{ $client->clientType?->title ?? __('crm::crm.not_specified') }}
                                        </td>
                                        <td>
                                            {{ $client->assignedUser?->name ?? __('crm::crm.not_assigned') }}
                                        </td>
                                        {{-- <td>
                                            @if ($client->type === 'person')
                                                @if ($client->gender === 'male')
                                                    <span class="badge bg-primary">ذكر</span>
                                                @elseif ($client->gender === 'female')
                                                    <span class="badge bg-pink">أنثى</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">—</span>
                                            @endif
                                        </td> --}}

                                        <td>
                                            @can('edit CRM Clients')
                                                <span class="d-inline-flex align-items-center">
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input toggle-active"
                                                            data-id="{{ $client->id }}"
                                                            {{ $client->is_active ? 'checked' : '' }}>
                                                    </div>
                                                </span>
                                            @else
                                                <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $client->is_active ? __('crm::crm.active') : __('crm::crm.inactive') }}
                                                </span>
                                            @endcan
                                        </td>

                                        @canany(['edit CRM Clients', 'delete CRM Clients'])
                                            <td>
                                                @can('view CRM Clients')
                                                    <a class="btn btn-primary btn-icon-square-sm"
                                                        href="{{ route('clients.show', $client->id) }}">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('edit CRM Clients')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('clients.edit', $client->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete CRM Clients')
                                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('crm::crm.are_you_sure_delete_client') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_clients_found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $clients->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('.toggle-active').forEach((el) => {
            el.addEventListener('change', function() {
                let clientId = this.getAttribute('data-id');
                let newStatus = this.checked ? '1' : '0';

                fetch("{{ url('crm/clients') }}/" + clientId + "/toggle-active", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            alert("{{ __('crm::crm.an_error_occurred_while_updating') }}");
                            this.checked = !this.checked;
                        }
                    })
                    .catch(() => {
                        alert("{{ __('crm::crm.connection_error_occurred') }}");
                        this.checked = !this.checked;
                    });
            });
        });
        let searchTimeout;
        document.getElementById('search-input').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('clients-filter-form').submit();
            }, 500);
        });


    </script>
@endpush
