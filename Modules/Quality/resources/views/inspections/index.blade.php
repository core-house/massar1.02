@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        {{ __("quality::quality.quality inspections") }}
                    </h2>
                </div>
                @can('create inspections')
                <div>
                    <a href="{{ route('quality.inspections.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>{{ __("quality::quality.new inspection") }}
                    </a>
                </div>
                @endcan

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __("quality::quality.inspection number") }}</th>
                            <th>{{ __("quality::quality.item") }}</th>
                            <th>{{ __("quality::quality.type") }}</th>
                            <th>{{ __("quality::quality.quantity") }}</th>
                            <th>{{ __("quality::quality.result") }}</th>
                            <th>{{ __("quality::quality.pass percentage") }}</th>
                            <th>{{ __("quality::quality.date") }}</th>
                            @canany(['edit inspections', 'delete inspections' , 'view inspections'])
                            <th>{{ __("quality::quality.actions") }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspections as $inspection)
                        <tr>
                            <td>
                                <a href="{{ route('quality.inspections.show', $inspection) }}">
                                    {{ $inspection->inspection_number }}
                                </a>
                            </td>
                            <td>{{ $inspection->item->name ?? '---' }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ match($inspection->inspection_type) {
                                        'receiving' => __('quality::quality.receiving'),
                                        'in_process' => __('quality::quality.in process'),
                                        'final' => __('quality::quality.final'),
                                        'random' => __('quality::quality.random'),
                                        'customer_complaint' => __('quality::quality.customer complaint'),
                                        default => $inspection->inspection_type
                                    } }}
                                </span>
                            </td>
                            <td>{{ number_format($inspection->quantity_inspected, 2) }}</td>
                            <td>
                                @if($inspection->result == 'pass')
                                    <span class="badge bg-success">{{ __('quality::quality.pass') }}</span>
                                @elseif($inspection->result == 'fail')
                                    <span class="badge bg-danger">{{ __('quality::quality.fail') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('quality::quality.conditional') }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="{{ $inspection->pass_percentage >= 95 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($inspection->pass_percentage, 1) }}%
                                </strong>
                            </td>
                            <td>{{ $inspection->inspection_date->format('Y-m-d') }}</td>
                            @canany(['edit inspections', 'delete inspections' , 'view inspections'])
                            <td>
                                <div class="btn-group" role="group">
                                    @can('view inspections')
                                    <a href="{{ route('quality.inspections.show', $inspection) }}"
                                       class="btn btn-sm btn-info" title="{{ __("quality::quality.view") }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('edit inspections')
                                    <a href="{{ route('quality.inspections.edit', $inspection) }}"
                                       class="btn btn-sm btn-warning" title="{{ __("quality::quality.edit") }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete inspections')
                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $inspection->id }}"
                                            title="{{ __("quality::quality.delete") }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $inspection->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('quality::quality.confirm delete') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{ __('quality::quality.are you sure you want to delete inspection number') }} {{ $inspection->inspection_number }}?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("quality::quality.cancel") }}</button>
                                                <form action="{{ route('quality.inspections.destroy', $inspection) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">{{ __("quality::quality.delete") }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @endcanany
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">{{ __('quality::quality.no inspections') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($inspections->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $inspections->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

