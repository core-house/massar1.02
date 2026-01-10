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
                <h4 class="mb-0 fw-bold" style="color: #444;">{{ __('general.daily_progress_title') }}</h4>
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
                    const sidebar = document.querySelector('.left-sidenav');
                    const pageWrapper = document.querySelector('.page-wrapper');
                    const toggleIcon = document.getElementById('sidebar-toggle-icon');
                    
                    if (!sidebar || !pageWrapper) return;

                    // Toggle logic (Checking typical Metrica 'enlarge-menu' class or display property)
                    // Since we are in valid standard structure, simple display toggle might work 
                    // BUT better to check if 'enlarge-menu' body class is used by theme.
                    // For now, simpler display toggle similar to original script logic:
                    
                    const isHidden = sidebar.style.display === 'none';

                    if (isHidden) {
                        sidebar.style.display = 'block';
                        pageWrapper.style.marginLeft = ''; // Reset to CSS default (usually 250px)
                        if(toggleIcon) { toggleIcon.classList.remove('fa-bars'); toggleIcon.classList.add('fa-times'); }
                    } else {
                        sidebar.style.display = 'none';
                        pageWrapper.style.marginLeft = '0';
                        if(toggleIcon) { toggleIcon.classList.remove('fa-times'); toggleIcon.classList.add('fa-bars'); }
                    }
                }
            </script>
        </ul>
    </nav>
</div>
