@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('maintenance::maintenance.periodic_maintenance'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('maintenance::maintenance.periodic_maintenance')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Periodic Maintenance')
                <a href="{{ route('periodic.maintenances.create') }}" type="button" class="btn btn-primary font-hold fw-bold">
                    {{ __('maintenance::maintenance.add_periodic_maintenance') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="periodic-table" filename="periodic-maintenance"
                            excel-label="{{ __('maintenance::maintenance.export_excel') }}"
                            pdf-label="{{ __('maintenance::maintenance.export_pdf') }}"
                            print-label="{{ __('maintenance::maintenance.print') }}" />

                        <table id="periodic-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('maintenance::maintenance.client') }}</th>
                                    <th>{{ __('maintenance::maintenance.item') }}</th>
                                    <th>{{ __('maintenance::maintenance.service_type') }}</th>
                                    <th>{{ __('maintenance::maintenance.frequency') }}</th>
                                    <th>{{ __('maintenance::maintenance.next_maintenance') }}</th>
                                    <th>{{ __('maintenance::maintenance.last_maintenance') }}</th>
                                    <th>{{ __('maintenance::maintenance.status') }}</th>
                                    @canany(['edit Periodic Maintenance', 'delete Periodic Maintenance'])
                                        <th>{{ __('maintenance::maintenance.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $schedule->client_name }}
                                            <br>
                                            <small class="text-muted">{{ $schedule->client_phone }}</small>
                                        </td>
                                        <td>
                                            {{ $schedule->item_name }}
                                            <br>
                                            <small class="text-muted">{{ __('maintenance::maintenance.item_number') }}: {{ $schedule->item_number }}</small>
                                        </td>
                                        <td>{{ $schedule->serviceType->name }}</td>
                                        <td>{{ $schedule->getFrequencyLabel() }}</td>
                                        <td>
                                            {{ $schedule->next_maintenance_date->format('Y-m-d') }}
                                            @if ($schedule->isOverdue())
                                                <br><span class="badge bg-danger">{{ __('maintenance::maintenance.overdue') }}</span>
                                            @elseif($schedule->isMaintenanceDueSoon())
                                                <br><span class="badge bg-warning">{{ __('maintenance::maintenance.due_soon') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $schedule->last_maintenance_date?->format('Y-m-d') ?? __('maintenance::maintenance.not_done_yet') }}</td>
                                        <td>
                                            @if ($schedule->is_active)
                                                <span class="badge bg-success">{{ __('maintenance::maintenance.active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('maintenance::maintenance.inactive') }}</span>
                                            @endif
                                        </td>
                                        @canany(['edit Periodic Maintenance', 'delete Periodic Maintenance'])
                                            <td>
                                                @can('edit Periodic Maintenance')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('periodic.maintenances.edit', $schedule->id) }}"
                                                        title="{{ __('maintenance::maintenance.edit') }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    <form action="{{ route('periodic.maintenances.toggle', $schedule->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-warning btn-icon-square-sm"
                                                            title="{{ __('maintenance::maintenance.toggle_status') }}">
                                                            <i class="las la-toggle-{{ $schedule->is_active ? 'on' : 'off' }}"></i>
                                                        </button>
                                                    </form>
                                                    <a class="btn btn-info btn-icon-square-sm"
                                                        href="{{ route('periodic.maintenances.create-maintenance', $schedule->id) }}"
                                                        title="{{ __('maintenance::maintenance.create_from_schedule_title') }}">
                                                        <i class="las la-plus"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Periodic Maintenance')
                                                    <form action="{{ route('periodic.maintenances.destroy', $schedule->id) }}"
                                                        method="POST" style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('maintenance::maintenance.are_you_sure_delete') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm"
                                                            title="{{ __('maintenance::maintenance.delete') }}">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-info py-3 mb-0" style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('maintenance::maintenance.no_periodic_schedules') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
