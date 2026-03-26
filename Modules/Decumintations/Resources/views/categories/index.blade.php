@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.decumintations')
@endsection

@section('title', __('decumintations.categories'))

@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">{{ __('decumintations.categories') }}</h4>
            @can('create Document Categories')
                <a href="{{ route('document-categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="las la-plus"></i> {{ __('decumintations.add_category') }}
                </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('decumintations.category_name') }}</th>
                            <th>{{ __('decumintations.color') }}</th>
                            <th>{{ __('decumintations.documents_count') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <i class="{{ $category->icon }}" style="color: {{ $category->color }}"></i>
                                    {{ $category->name }}
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $category->color }}">
                                        {{ $category->color }}
                                    </span>
                                </td>
                                <td>{{ $category->documents_count }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @can('edit Document Categories')
                                            <a href="{{ route('document-categories.edit', $category) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="las la-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete Document Categories')
                                            <form action="{{ route('document-categories.destroy', $category) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ __('decumintations.no_categories') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $categories->links() }}</div>
        </div>
    </div>
</div>
@endsection
