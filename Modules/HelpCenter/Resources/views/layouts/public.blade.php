<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <title>{{ __('helpcenter::helpcenter.title') }} - MASAR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app-rtl.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/masar-themes.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; background: #f5f7fa; }
        .hc-navbar { background: #fff; border-bottom: 1px solid #e9ecef; padding: 0.75rem 1.5rem; position: sticky; top: 0; z-index: 100; }
        .hc-brand { color: #34d3a3; font-weight: 700; font-size: 1.1rem; text-decoration: none; }
        .hc-brand:hover { color: #28b89a; }
        .hc-main { min-height: calc(100vh - 60px); padding: 2rem 0; }
    </style>
</head>
<body class="theme-mint-green">

{{-- Navbar --}}
<nav class="hc-navbar d-flex align-items-center justify-content-between">
    <a href="{{ route('helpcenter.index') }}" class="hc-brand">
        <i class="fas fa-book me-2"></i>{{ __('helpcenter::helpcenter.title') }}
    </a>

    <div class="d-flex align-items-center gap-3">
        {{-- Search --}}
        <div class="position-relative" x-data="massarHelpNavSearch()">
            <div class="input-group input-group-sm" style="width: 260px;">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-start-0"
                       placeholder="{{ __('helpcenter::helpcenter.search_placeholder') }}"
                       x-model="query"
                       @input.debounce.400ms="search()"
                       @keydown.escape="results = []"
                       autocomplete="off">
            </div>
            <div class="position-absolute bg-white border rounded shadow"
                 style="top: 100%; width: 100%; z-index: 200; margin-top: 4px;"
                 x-show="results.length > 0" x-cloak>
                <template x-for="item in results" :key="item.id">
                    <a :href="item.url"
                       class="d-flex align-items-center px-3 py-2 text-decoration-none border-bottom hc-search-item">
                        <i class="fas fa-file-alt me-2 text-muted small"></i>
                        <div>
                            <div x-text="item.title" class="small fw-semibold text-dark"></div>
                            <small x-text="item.category" class="text-muted" style="font-size:.72rem;"></small>
                        </div>
                    </a>
                </template>
            </div>
        </div>

        @auth
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-home me-1"></i>{{ __('navigation.home') }}
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-sign-in-alt me-1"></i>{{ __('auth.login') }}
            </a>
        @endauth
    </div>
</nav>

{{-- Content --}}
<main class="hc-main">
    @yield('content')
</main>

<style>
.hc-search-item:hover { background: #f8f9fa; }
.hc-search-item:last-child { border-bottom: none !important; }
[x-cloak] { display: none !important; }
</style>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@vite(['resources/js/app.js'])

<script>
function massarHelpNavSearch() {
    return {
        query: '',
        results: [],
        search() {
            if (this.query.length < 2) { this.results = []; return; }
            fetch(`{{ route('helpcenter.search') }}?q=${encodeURIComponent(this.query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(d => { this.results = d; });
        }
    };
}
</script>

</body>
</html>
