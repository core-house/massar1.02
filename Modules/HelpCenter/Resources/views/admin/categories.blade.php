@extends('dashboard')
@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0"><i class="fas fa-folder me-2" style="color:#34d3a3;"></i>{{ __('helpcenter::helpcenter.categories') }}</h4>
        <a href="{{ route('helpcenter.admin.articles') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-file-alt me-1"></i>{{ __('helpcenter::helpcenter.articles') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Add Form --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0">{{ __('helpcenter::helpcenter.add_category') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('helpcenter.admin.categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.name_ar') }}</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.name_en') }}</label>
                            <input type="text" name="name_en" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.icon') }} <small class="text-muted">(fas fa-...)</small></label>
                            <input type="text" name="icon" class="form-control" value="fas fa-folder">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.sort_order') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i>{{ __('helpcenter::helpcenter.add') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- List --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('helpcenter::helpcenter.name_ar') }}</th>
                                <th>{{ __('helpcenter::helpcenter.icon') }}</th>
                                <th>{{ __('helpcenter::helpcenter.articles_count') }}</th>
                                <th>{{ __('helpcenter::helpcenter.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                            <tr x-data="{ editing: false }">
                                <td>
                                    <span x-show="!editing">
                                        <i class="{{ $cat->icon }} me-2" style="color:#34d3a3;"></i>{{ $cat->name }}
                                    </span>
                                    <form x-show="editing" method="POST"
                                          action="{{ route('helpcenter.admin.categories.update', $cat) }}">
                                        @csrf @method('PUT')
                                        <div class="d-flex gap-2">
                                            <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm">
                                            <input type="text" name="icon" value="{{ $cat->icon }}" class="form-control form-control-sm" style="width:130px;">
                                            <input type="hidden" name="sort_order" value="{{ $cat->sort_order }}">
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                        </div>
                                    </form>
                                </td>
                                <td><code>{{ $cat->icon }}</code></td>
                                <td>{{ $cat->articles_count }}</td>
                                <td>
                                    <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $cat->is_active ? __('helpcenter::helpcenter.active') : __('helpcenter::helpcenter.inactive') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button @click="editing = !editing" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('helpcenter.admin.categories.destroy', $cat) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('{{ __('helpcenter::helpcenter.confirm_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ __('helpcenter::helpcenter.no_categories') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
