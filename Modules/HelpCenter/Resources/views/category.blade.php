@extends('helpcenter::layouts.public')
@section('content')

<div class="container-fluid py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('helpcenter.index') }}">{{ __('helpcenter::helpcenter.title') }}</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center mb-4">
        <i class="{{ $category->icon }} fa-2x me-3" style="color:#34d3a3;"></i>
        <h3 class="mb-0">{{ $category->name }}</h3>
    </div>

    <div class="row g-3">
        @forelse($articles as $article)
        <div class="col-12">
            <a href="{{ route('helpcenter.article', $article->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm help-article-row">
                    <div class="card-body d-flex align-items-center py-3">
                        <i class="fas fa-file-alt me-3 text-muted"></i>
                        <div class="flex-grow-1">
                            <span class="fw-semibold text-dark">{{ $article->title }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-muted small">
                            <span><i class="fas fa-eye me-1"></i>{{ $article->views_count }}</span>
                            <i class="fas fa-chevron-left"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i class="fas fa-file-alt fa-3x mb-3 d-block" style="opacity:.3;"></i>
            {{ __('helpcenter::helpcenter.no_articles') }}
        </div>
        @endforelse
    </div>

</div>

<style>
.help-article-row { transition: transform .15s, box-shadow .15s; }
.help-article-row:hover { transform: translateX(-3px); box-shadow: 0 4px 15px rgba(52,211,163,.15) !important; }
</style>

@endsection
