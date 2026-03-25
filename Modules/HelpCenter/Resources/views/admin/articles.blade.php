@extends('dashboard')
@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0"><i class="fas fa-file-alt me-2" style="color:#34d3a3;"></i>{{ __('helpcenter::helpcenter.articles') }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('helpcenter.admin.categories') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-folder me-1"></i>{{ __('helpcenter::helpcenter.categories') }}
            </a>
            <a href="{{ route('helpcenter.admin.articles.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>{{ __('helpcenter::helpcenter.add_article') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('helpcenter::helpcenter.title') }}</th>
                        <th>{{ __('helpcenter::helpcenter.category') }}</th>
                        <th>{{ __('helpcenter::helpcenter.route_key') }}</th>
                        <th>{{ __('helpcenter::helpcenter.status') }}</th>
                        <th>{{ __('helpcenter::helpcenter.views') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td>{{ $article->title }}</td>
                        <td>{{ $article->category?->name }}</td>
                        <td><code class="small">{{ $article->route_key ?? '-' }}</code></td>
                        <td>
                            <span class="badge {{ $article->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ __('helpcenter::helpcenter.' . $article->status) }}
                            </span>
                        </td>
                        <td>{{ $article->views_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('helpcenter.admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('helpcenter.admin.articles.destroy', $article) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('{{ __('helpcenter::helpcenter.confirm_delete') }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('helpcenter::helpcenter.no_articles') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent">
            {{ $articles->links() }}
        </div>
    </div>
</div>

@endsection
