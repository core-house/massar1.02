@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('maintenance::maintenance.maintenance'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('maintenance::maintenance.maintenance')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Maintenances')
                <a href="{{ route('maintenances.create') }}" type="button" class="btn btn-primary font-hold fw-bold">
                    {{ __('maintenance::maintenance.add_new') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br><br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="maintenances-table" filename="maintenances"
                            excel-label="{{ __('maintenance::maintenance.export_excel') }}"
                            pdf-label="{{ __('maintenance::maintenance.export_pdf') }}"
                            print-label="{{ __('maintenance::maintenance.print') }}" />
                        <table id="maintenances-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('maintenance::maintenance.client_name') }}</th>
                                    <th>{{ __('maintenance::maintenance.client_phone') }}</th>
                                    <th>{{ __('maintenance::maintenance.item_name') }}</th>
                                    <th>{{ __('maintenance::maintenance.item_number') }}</th>
                                    <th>{{ __('maintenance::maintenance.service_type') }}</th>
                                    <th>{{ __('maintenance::maintenance.date') }}</th>
                                    <th>{{ __('maintenance::maintenance.accural_date') }}</th>
                                    <th>{{ __('maintenance::maintenance.status') }}</th>
                                    @canany(['edit Maintenances', 'delete Maintenances'])
                                        <th>{{ __('maintenance::maintenance.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($maintenances as $maintenance)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $maintenance->client_name }}</td>
                                        <td>{{ $maintenance->client_phone }}</td>
                                        <td>{{ $maintenance->item_name }}</td>
                                        <td>{{ $maintenance->item_number }}</td>
                                        <td>{{ $maintenance->type->name }}</td>
                                        <td>{{ $maintenance->date ? $maintenance->date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $maintenance->accural_date ? $maintenance->accural_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            @if ($maintenance->status)
                                                {{ $maintenance->status->label() }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @canany(['edit Maintenances', 'delete Maintenances'])
                                            <td>
                                                @can('edit Maintenances')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('maintenances.edit', $maintenance->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Maintenances')
                                                    <form action="{{ route('maintenances.destroy', $maintenance->id) }}"
                                                        method="POST" style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('maintenance::maintenance.are_you_sure_delete') }}');">
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
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info py-3 mb-0" style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('maintenance::maintenance.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $maintenances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
