<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@include('admin.partials.head')

<body class="daily-progress-layout">
    {{-- YouTube-style Progress Bar Loader --}}
    <div id="page-loader" class="page-loader">
        <div class="loader-bar"></div>
    </div>
    
    {{-- Custom Progress Sidebar --}}
    @include('progress::layouts.sidebar')

    <div class="page-wrapper">
        {{-- Custom Progress Navbar --}}
        @include('progress::layouts.navbar')
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    @include('sweetalert::alert')
                    @yield('content')
                </div>
            </div>
            @include('admin.partials.footer')
        </div>
    </div>
    @include('admin.partials.scripts')
    @yield('script')
</body>
</html>
