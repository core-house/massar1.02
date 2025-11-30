@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Maintenance'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Maintenance')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Maintenances')
                <a href="{{ route('maintenances.create') }}" type="button" class="btn btn-primary font-hold fw-bold">
                    {{ __('Add New') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br>
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="maintenances-table" filename="maintenances"
                            excel-label="{{ __('Export Excel') }}" pdf-label="{{ __('Export PDF') }}"
                            print-label="{{ __('Print') }}" />
                        <table id="maintenances-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Client Name') }}</th>
                                    <th>{{ __('Client Phone') }}</th>
                                    <th>{{ __('Item Name') }}</th>
                                    <th>{{ __('Item Number') }}</th>
                                    <th>{{ __('Service Type') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Accural Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @canany(['edit Maintenances', 'delete Maintenances'])
                                        <th>{{ __('Actions') }}</th>
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
                                        <td>{{ $maintenance->accural_date ? $maintenance->accural_date->format('Y-m-d') : '-' }}
                                        </td>
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
                                                        onsubmit="return confirm('{{ __('Are you sure you want to delete this item?') }}');">
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
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('No data available') }}
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
