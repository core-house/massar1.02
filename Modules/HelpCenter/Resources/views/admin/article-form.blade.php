@extends('dashboard')
@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('helpcenter.admin.articles') }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="fas fa-arrow-right"></i>
        </a>
        <h4 class="mb-0">
            {{ isset($article) ? __('helpcenter::helpcenter.edit_article') : __('helpcenter::helpcenter.add_article') }}
        </h4>
    </div>

    <form method="POST"
          action="{{ isset($article) ? route('helpcenter.admin.articles.update', $article) : route('helpcenter.admin.articles.store') }}">
        @csrf
        @if(isset($article)) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.title_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $article->title ?? '') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.title_en') }}</label>
                            <input type="text" name="title_en" class="form-control"
                                   value="{{ old('title_en', $article->title_en ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.content_ar') }} <span class="text-danger">*</span></label>
                            <textarea name="content" id="content-ar" class="form-control @error('content') is-invalid @enderror"
                                      rows="12">{{ old('content', $article->content ?? '') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.content_en') }}</label>
                            <textarea name="content_en" id="content-en" class="form-control" rows="8">{{ old('content_en', $article->content_en ?? '') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('helpcenter::helpcenter.select_category') }} --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $article->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('helpcenter::helpcenter.route_key') }}
                                <i class="fas fa-info-circle text-muted" title="{{ __('helpcenter::helpcenter.route_key_hint') }}"></i>
                            </label>
                            <input type="text" name="route_key" class="form-control"
                                   placeholder="e.g. vouchers.index"
                                   value="{{ old('route_key', $article->route_key ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ old('status', $article->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                                    {{ __('helpcenter::helpcenter.draft') }}
                                </option>
                                <option value="published" {{ old('status', $article->status ?? '') === 'published' ? 'selected' : '' }}>
                                    {{ __('helpcenter::helpcenter.published') }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('helpcenter::helpcenter.sort_order') }}</label>
                            <input type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', $article->sort_order ?? 0) }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>{{ __('helpcenter::helpcenter.save') }}
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
