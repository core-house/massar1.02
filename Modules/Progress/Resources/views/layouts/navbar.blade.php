<div class="topbar">
    <!-- Navbar -->
    <nav class="navbar-custom d-flex justify-content-between align-items-center">
        
        <!-- Left Side: Toggle & Title -->
        <ul class="list-unstyled topbar-nav mb-0 d-flex align-items-center">
            
            {{-- Sidebar Toggle Button --}}
            <li class="me-3">
                <button type="button" 
                        id="sidebar-toggle-btn" 
                        class="btn btn-lg transition-base sidebar-toggle-btn"
                        title="{{ __('Show/Hide Menu') }}"
                        onclick="toggleSidebarMenu()"
                        style="background: none; border: none; cursor: pointer; padding: 8px 12px;">
                    <i id="sidebar-toggle-icon" class="fas fa-bars fa-2x" style="color: #34d3a3;"></i>
                </button>
            </li>

            <!-- Module Title -->
            <li class="d-none d-md-block">
                <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #444;">
                    <i class="las la-user-circle me-2 fs-4"></i>
                    {{ auth()->user()->name ?? '' }}
                </h5>
            </li>
        </ul>

        <!-- Right Side: Tools -->
        <ul class="list-unstyled topbar-nav mb-0 d-flex align-items-center">

            <!-- Language Switcher -->
            <li class="me-3">
                @livewire('language-switcher')
            </li>

             <!-- Logout -->
             <li>
                <button type="button" class="btn btn-lg transition-base logout-btn"
                    title="{{ __('navigation.logout') }}" onclick="confirmLogout()"
                    style="background: none; border: none; color: #34d3a3; cursor: pointer;">
                    <i class="fas fa-sign-out-alt fa-2x" style="color: #34d3a3;"></i>
                </button>

                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
            </li>

            <script>
                function confirmLogout() {
                    Swal.fire({
                        title: '{{ __('general.confirm') }}',
                        text: "{{ __('general.are_you_sure') }}",
                        icon: 'warning',
                        iconColor: '#34d3a3',
                        showCancelButton: true,
                        confirmButtonColor: '#34d3a3',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '{{ __('general.logout') }}'
                    }).then((result) => {
                        if (result.isConfirmed) {
                             document.getElementById('logout-form').submit();
                        }
                    });
                }

                // Standard Toggle Script matching standard template
                function toggleSidebarMenu() {
                    document.body.classList.toggle('enlarge-menu');
                    
                    // Update icon
                    const toggleIcon = document.getElementById('sidebar-toggle-icon');
                    if (document.body.classList.contains('enlarge-menu')) {
                        toggleIcon.classList.remove('fa-bars');
                        toggleIcon.classList.add('fa-align-left');
                    } else {
                        toggleIcon.classList.remove('fa-align-left');
                        toggleIcon.classList.add('fa-bars');
                    }
                }
            </script>
            
            <style>
                /* Sidebar Transition */
                .left-sidenav, .page-wrapper {
                    transition: all 0.3s ease-in-out;
                }

                /* Enlarge Menu State (Minimized Sidebar) */
                body.enlarge-menu .left-sidenav {
                    width: 70px !important;
                }
                body.enlarge-menu .page-wrapper {
                    margin-left: 70px !important;
                }
                
                /* Hide Text Elements */
                body.enlarge-menu .left-sidenav .nav-link span,
                body.enlarge-menu .left-sidenav .logo-text,
                body.enlarge-menu .left-sidenav .menu-label,
                body.enlarge-menu .left-sidenav #internal-sidebar-close {
                    display: none !important;
                }

                /* Center Icons */
                body.enlarge-menu .left-sidenav .nav-link {
                    justify-content: center !important;
                    padding: 15px 0 !important;
                }
                body.enlarge-menu .left-sidenav .menu-icon {
                    margin-right: 0 !important;
                    font-size: 1.3rem;
                }
                
                /* Adjust Brand Box */
                body.enlarge-menu .brand-box {
                    padding: 15px 0 !important;
                    justify-content: center !important;
                }
            </style>

        </ul>
    </nav>
</div>
