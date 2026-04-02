@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('myresources.resource_types') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">{{ __('myresources.types_list') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('myresources.types.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('myresources.add_new_type') }}
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
                                    <th>{{ __('myresources.main_category') }}</th>
                                    <th>{{ __('myresources.name') }}</th>
                                    <th>{{ __('myresources.arabic_name') }}</th>
                                    <th>{{ __('myresources.description') }}</th>
                                    <th>{{ __('myresources.status') }}</th>
                                    <th>{{ __('myresources.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($types as $type)
                                    <tr>
                                        <td>{{ $type->id }}</td>
                                        <td>
                                            <span class="badge bg-{{ $type->category->color ?? 'primary' }}">
                                                {{ $type->category->display_name ?? __('common.not_available') }}
                                            </span>
                                        </td>
                                        <td>{{ $type->name }}</td>
                                        <td>{{ $type->name_ar }}</td>
                                        <td>{{ $type->description ?? '-' }}</td>
                                        <td>
                                            @if($type->is_active)
                                                <span class="badge bg-success">{{ __('myresources.active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('common.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('myresources.types.edit', $type) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('myresources.types.destroy', $type) }}" method="POST" class="d-inline"
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
                                        <td colspan="7" class="text-center">{{ __('myresources.no_types_found') }}</td>
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
