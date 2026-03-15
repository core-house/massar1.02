@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.activities'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.activities')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Activities')
                <div class="mb-4">
                    <a href="{{ route('activities.create') }}" class="btn btn-main">
                        <i class="fas fa-plus me-2"></i>
                        {{ __('crm::crm.add_new_activity') }}
                    </a>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="activities-table" filename="activities-report" :excel-label="__('crm::crm.export_excel')"
                            :pdf-label="__('crm::crm.export_pdf')" :print-label="__('crm::crm.print')" />

                        <table id="activities-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('crm::crm.title') }}</th>
                                    <th>{{ __('crm::crm.type') }}</th>
                                    <th>{{ __('crm::crm.date') }}</th>
                                    <th>{{ __('crm::crm.time') }}</th>
                                    <th>{{ __('crm::crm.client') }}</th>
                                    <th>{{ __('crm::crm.assigned_to') }}</th>
                                    <th>{{ __('crm::crm.description') }}</th>
                                    @canany(['edit Activities', 'delete Activities'])
                                        <th>{{ __('crm::crm.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr class="text-center align-middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $activity->title }}</td>
                                        <td>
                                            @if ($activity->type->value === \Modules\CRM\Enums\ActivityTypeEnum::CALL->value)
                                                <span class="badge bg-primary">{{ $activity->type->label() }}</span>
                                            @elseif ($activity->type->value === \Modules\CRM\Enums\ActivityTypeEnum::MESSAGE->value)
                                                <span class="badge bg-success">{{ $activity->type->label() }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ $activity->type->label() }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($activity->activity_date)->format('Y-m-d') }}</td>
                                        <td>{{ optional($activity->scheduled_at)->format('H:i A') }}</td>
                                        <td>{{ optional($activity->client)->cname ?? __('N/A') }}</td>
                                        <td>{{ optional($activity->assignedUser)->name ?? __('crm::crm.unassigned') }}</td>
                                        <td>{{ Str::limit($activity->description, 30) }}</td>

                                        @canany(['edit Activities', 'delete Activities'])
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @can('edit Activities')
                                                        <a class="btn btn-success btn-sm"
                                                            href="{{ route('activities.edit', $activity->id) }}"
                                                            title="{{ __('crm::crm.edit') }}">
                                                            <i class="las la-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete Activities')
                                                        <form action="{{ route('activities.destroy', $activity->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this activity?') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                title="{{ __('crm::crm.delete') }}">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-info py-3 mb-0">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
