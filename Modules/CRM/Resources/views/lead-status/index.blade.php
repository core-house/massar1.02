@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.lead_statuses'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.lead_statuses')],
        ],
    ])
    <div class="row">
        <div class="col-lg-12">
            @can('create Lead Statuses')
                <a href="{{ route('lead-status.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    {{ __('crm::crm.add_new') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br>
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="lead-status-table" filename="lead-status-table" :excel-label="__('crm::crm.export_excel')"
                            :pdf-label="__('crm::crm.export_pdf')" :print-label="__('crm::crm.print')" />

                        <table id="lead-status-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('crm::crm.name') }}</th>
                                    <th>{{ __('crm::crm.color') }}</th>
                                    <th>{{ __('crm::crm.order') }}</th>
                                    @canany(['delete Lead Statuses', 'edit Lead Statuses'])
                                        <th>{{ __('crm::crm.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leadStatus as $chance)
                                    <tr class="text-center">
                                        <td> {{ $loop->iteration }} </td>
                                        <td>{{ $chance->name }}</td>

                                        <td>
                                            <span
                                                style="display:inline-block; width: 20px; height: 20px;
                                                background-color: {{ $chance->color }};
                                                 border: 1px solid #ccc; border-radius: 4px;"></span>
                                            <span>{{ $chance->color }}</span>
                                        </td>
                                        <td>{{ $chance->order_column }}</td>
                                        @canany(['edit Lead Statuses', 'delete Lead Statuses'])
                                            <td>
                                                @can('edit Lead Statuses')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('lead-status.edit', $chance->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Lead Statuses')
                                                    <form action="{{ route('lead-status.destroy', $chance->id) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('crm::crm.confirm_delete_status') }}');">
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
                                        <td colspan="13" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_data_added_yet') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
