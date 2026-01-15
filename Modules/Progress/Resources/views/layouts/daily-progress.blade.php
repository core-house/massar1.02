<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@include('progress::layouts.progress-head')

<body class="daily-progress-layout">

    {{-- YouTube-style Progress Bar Loader --}}
    <div id="page-loader" class="page-loader">
        <div class="loader-bar"></div>
    </div>
    
    {{-- Custom Progress Navbar --}}
    @include('progress::layouts.navbar')

    <div class="container-fluid">
        <div class="row">
            {{-- Custom Progress Sidebar --}}
            @include('progress::layouts.sidebar')

            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 page-wrapper">
                <div class="page-content pt-4"> <!-- Added padding top -->
                    <div class="container-fluid">
                        <div class="row">
                            @include('sweetalert::alert')
                            @yield('content')
                        </div>
                    </div>
                    @include('admin.partials.footer')
                </div>
            </div>
        </div>
    </div>
    @include('admin.partials.scripts')
    
    <script>
        // Check for saved theme
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }

        // Sidebar Toggle
        function toggleSidebar() {
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                const sidebar = document.getElementById('sidebarMenu');
                if (sidebar) {
                    sidebar.classList.toggle('show');
                }
            } else {
                document.body.classList.toggle('sidebar-collapsed');
                // Persist state
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
            }
        }

        // Initialize state on load
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 768) {
                document.body.classList.add('sidebar-collapsed');
            }
        });

        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');
                    const isDark = document.body.classList.contains('dark-mode');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    
                    // Update icon if exists
                    if (darkModeIcon) {
                        darkModeIcon.classList.toggle('fa-moon', !isDark);
                        darkModeIcon.classList.toggle('fa-sun', isDark);
                    }
                });
            }
        });
    </script>
    @yield('script')
</body>
</html>
