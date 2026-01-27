<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@include('admin.partials.head')

<body>
    {{-- YouTube-style Progress Bar Loader --}}
    <div id="page-loader" class="page-loader">
        <div class="loader-bar"></div>
    </div>
    @hasSection('sidebar')
        <div class="left-sidenav">
            <div class="menu-content h-100" data-simplebar>
                <ul class="metismenu left-sidenav-menu">
                    @yield('sidebar')
                </ul>
            </div>
        </div>
    @else
        @include('tenancy::layouts.admin-sidebar')
    @endif

    <div class="page-wrapper">
        @include('tenancy::layouts.admin-topbar')
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
