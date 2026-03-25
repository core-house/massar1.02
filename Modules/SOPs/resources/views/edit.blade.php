@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('sops::sops.edit'),
        'breadcrumb_items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('sops::sops.sops'), 'url' => route('sops.index')],
            ['label' => __('sops::sops.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('sops.update', $sop->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('sops::sops.title') }}</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $sop->title) }}" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('sops::sops.category') }}</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $sop->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('sops::sops.status') }}</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status', $sop->status) == 'draft' ? 'selected' : '' }}>{{ __('sops::sops.draft') }}</option>
                                    <option value="active" {{ old('status', $sop->status) == 'active' ? 'selected' : '' }}>{{ __('sops::sops.active') }}</option>
                                    <option value="archived" {{ old('status', $sop->status) == 'archived' ? 'selected' : '' }}>{{ __('sops::sops.archived') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('sops::sops.description') }}</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $sop->description) }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('sops::sops.version') }}</label>
                                <input type="text" name="version" class="form-control @error('version') is-invalid @enderror" value="{{ old('version', $sop->version) }}">
                                @error('version') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ __('sops::sops.attachment') }}</label>
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                                @if($sop->attachment)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $sop->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="las la-download"></i> {{ __('Current Attachment') }}
                                        </a>
                                    </div>
                                @endif
                                @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('sops::sops.content') }}</label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="10">{{ old('content', $sop->content) }}</textarea>
                                @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('sops.index') }}" class="btn btn-secondary px-4">{{ __('sops::sops.cancel') }}</a>
                                <button type="submit" class="btn btn-main px-4">{{ __('sops::sops.save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
