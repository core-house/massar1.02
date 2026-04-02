@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('myresources.resource_statuses') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">{{ __('myresources.statuses_list') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('myresources.statuses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('myresources.add_new_status') }}
                            </a>
                            <a href="{{ route('myresources.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> {{ __('myresources.resources_management') }}
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('myresources.name') }}</th>
                                    <th>{{ __('myresources.arabic_name') }}</th>
                                    <th>{{ __('myresources.description') }}</th>
                                    <th>{{ __('myresources.icon') }}</th>
                                    <th>{{ __('myresources.color') }}</th>
                                    <th>{{ __('myresources.sort_order') }}</th>
                                    <th>{{ __('myresources.status') }}</th>
                                    <th>{{ __('myresources.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statuses as $status)
                                    <tr>
                                        <td>{{ $status->id }}</td>
                                        <td>{{ $status->name }}</td>
                                        <td>{{ $status->name_ar }}</td>
                                        <td>{{ $status->description ?? '-' }}</td>
                                        <td>
                                            @if($status->icon)
                                                <i class="{{ $status->icon }}"></i>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($status->color)
                                                <span class="badge bg-{{ $status->color }}">{{ $status->color }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $status->sort_order ?? 0 }}</td>
                                        <td>
                                            @if($status->is_active)
                                                <span class="badge bg-success">{{ __('myresources.active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('common.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('myresources.statuses.edit', $status) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('myresources.statuses.destroy', $status) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('myresources.no_statuses_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
