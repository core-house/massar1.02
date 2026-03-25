@extends('helpcenter::layouts.public')
@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="mb-1"><i class="fas fa-book me-2" style="color:#34d3a3;"></i>{{ __('helpcenter::helpcenter.title') }}</h3>
            <p class="text-muted mb-0">{{ __('helpcenter::helpcenter.subtitle') }}</p>
        </div>
        @can('manage helpcenter')
        <div class="col-auto">
            <a href="{{ route('helpcenter.admin.articles') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-cog me-1"></i>{{ __('helpcenter::helpcenter.manage') }}
            </a>
        </div>
        @endcan
    </div>

    {{-- Search --}}
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <p class="text-center text-muted">{{ __('helpcenter::helpcenter.subtitle') }}</p>
        </div>
    </div>

    {{-- Categories --}}
    <div class="row g-3">
        @forelse($categories as $category)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('helpcenter.category', $category->slug) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm help-category-card">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="{{ $category->icon }} fa-2x" style="color:#34d3a3;"></i>
                        </div>
                        <h6 class="card-title mb-1">{{ $category->name }}</h6>
                        <small class="text-muted">{{ $category->active_articles_count }} {{ __('helpcenter::helpcenter.articles') }}</small>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i class="fas fa-folder-open fa-3x mb-3 d-block" style="opacity:.3;"></i>
            {{ __('helpcenter::helpcenter.no_categories') }}
        </div>
        @endforelse
    </div>

</div>

<style>
.help-category-card { transition: transform .2s, box-shadow .2s; cursor: pointer; }
.help-category-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(52,211,163,.2) !important; }
.help-search-item:hover { background: #f8f9fa; }
[x-cloak] { display: none !important; }
</style>



@endsection
