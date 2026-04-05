@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.decumintations')
@endsection

@section('title', __('decumintations::decumintations.documents'))

@section('content')
    <div class="row justify-content-center p-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('decumintations::decumintations.documents') }}</h4>
                    @can('create Documents')
                        <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
                            <i class="las la-plus"></i> {{ __('decumintations::decumintations.add_document') }}
                        </a>
                    @endcan
                </div>
                <div class="card-body">

                    {{-- فلتر التصنيفات --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select" onchange="window.location.href=this.value">
                                <option value="{{ route('documents.index') }}">
                                    {{ __('decumintations::decumintations.choose_category') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ route('documents.index', ['category_id' => $cat->id]) }}"
                                        {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('decumintations::decumintations.title') }}</th>
                                    <th>{{ __('decumintations::decumintations.category') }}</th>
                                    <th>{{ __('decumintations::decumintations.file_type') }}</th>
                                    <th>{{ __('decumintations::decumintations.expiry_date') }}</th>
                                    <th>{{ __('decumintations::decumintations.uploaded_by') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $document->title }}
                                            @if ($document->is_confidential)
                                                <span
                                                    class="badge bg-danger ms-1">{{ __('decumintations::decumintations.confidential') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->category)
                                                <span class="badge"
                                                    style="background-color: {{ $document->category->color }}">
                                                    {{ $document->category->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $document->file_type ?? '—' }}</td>
                                        <td>
                                            @if ($document->expiry_date)
                                                <span
                                                    class="{{ $document->expiry_date->isPast() ? 'text-danger' : ($document->expiry_date->diffInDays() <= 30 ? 'text-warning' : '') }}">
                                                    {{ $document->expiry_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $document->uploadedBy?->name ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('documents.download', $document) }}"
                                                    class="btn btn-icon-square-sm btn-outline-success"
                                                    title="{{ __('decumintations::decumintations.download') }}">
                                                    <i class="las la-download"></i>
                                                </a>
                                                @can('edit Documents')
                                                    <a href="{{ route('documents.edit', $document) }}"
                                                        class="btn btn-success btn-icon-square-sm"
                                                        title="{{ __('common.edit') }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Documents')
                                                    <form action="{{ route('documents.destroy', $document) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm"
                                                            title="{{ __('common.delete') }}">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            {{ __('decumintations::decumintations.no_documents') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
