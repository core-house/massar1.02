@extends('helpcenter::layouts.public')
@section('content')

<div class="container-fluid py-4">
    <div class="row">

        {{-- Article Content --}}
        <div class="col-lg-8">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('helpcenter.index') }}">{{ __('helpcenter::helpcenter.title') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('helpcenter.category', $article->category->slug) }}">{{ $article->category->name }}</a></li>
                    <li class="breadcrumb-item active">{{ $article->title }}</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-1">{{ $article->title }}</h2>
                    <div class="d-flex align-items-center gap-3 text-muted small mb-4">
                        <span><i class="fas fa-eye me-1"></i>{{ $article->views_count }} {{ __('helpcenter::helpcenter.views') }}</span>
                        <span><i class="fas fa-calendar me-1"></i>{{ $article->updated_at->diffForHumans() }}</span>
                    </div>

                    <div class="help-article-content">
                        {!! $article->content !!}
                    </div>
                </div>
            </div>

            {{-- Feedback --}}
            <div class="card border-0 shadow-sm mt-3" x-data="massarHelpFeedback({{ $article->id }})">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <span class="text-muted">{{ __('helpcenter::helpcenter.was_helpful') }}</span>
                    <template x-if="!submitted">
                        <div class="d-flex gap-2">
                            <button @click="send(true)" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-thumbs-up me-1"></i>{{ __('helpcenter::helpcenter.yes') }}
                            </button>
                            <button @click="send(false)" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-thumbs-down me-1"></i>{{ __('helpcenter::helpcenter.no') }}
                            </button>
                        </div>
                    </template>
                    <template x-if="submitted">
                        <span class="text-success"><i class="fas fa-check me-1"></i>{{ __('helpcenter::helpcenter.thanks') }}</span>
                    </template>
                </div>
            </div>

        </div>

        {{-- Related Articles --}}
        @if($related->isNotEmpty())
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="mb-0"><i class="fas fa-link me-2" style="color:#34d3a3;"></i>{{ __('helpcenter::helpcenter.related') }}</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($related as $rel)
                    <a href="{{ route('helpcenter.article', $rel->id) }}"
                       class="list-group-item list-group-item-action border-0 py-2">
                        <i class="fas fa-file-alt me-2 text-muted small"></i>{{ $rel->title }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
.help-article-content img { max-width: 100%; border-radius: 8px; }
.help-article-content h1,.help-article-content h2,.help-article-content h3 { margin-top: 1.5rem; }
.help-article-content pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; }
</style>

<script>
function massarHelpFeedback(articleId) {
    return {
        submitted: false,
        send(isHelpful) {
            fetch(`/help/article/${articleId}/feedback`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ is_helpful: isHelpful }),
            }).then(() => { this.submitted = true; });
        }
    };
}
</script>

@endsection
