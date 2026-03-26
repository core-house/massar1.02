<div class="topbar">
    <nav class="navbar-custom d-flex justify-content-between align-items-center">

        {{-- الجهة اليمنى (اللغة وتسجيل الخروج) --}}
        <ul class="list-unstyled topbar-nav mb-0 d-flex align-items-center">

            <li class="me-3">
                @livewire('language-switcher')
            </li>

            {{-- زر تسجيل الخروج --}}
            <li>
                <button type="button" class="btn btn-lg transition-base logout-btn" title="{{ __('navigation.logout') }}"
                    onclick="confirmLogout()" style="background: none; border: none; color: #34d3a3; cursor: pointer;">
                    <i class="fas fa-sign-out-alt fa-2x" style="color: #34d3a3;"></i>
                </button>

                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>

        <ul class="list-unstyled topbar-nav mb-0 d-flex align-items-center order-first">
        </ul>
    </nav>
</div>

{{-- تضمين السكريبتات الخاصة بالـ Logout والـ Sidebar (نفس اللي عندك) --}}
<script>
    function confirmLogout() {
        Swal.fire({
            title: '{{ __('هل أنت متأكد؟') }}',
            text: '{{ __('سيتم تسجيل خروجك من لوحة تحكم الإدارة') }}',
            icon: 'warning',
            iconColor: '#34d3a3',
            showCancelButton: true,
            confirmButtonColor: '#34d3a3',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، خروج',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    // سكريبت الـ Sidebar اللي أنت بتستخدمه
    function toggleSidebarMenu() {
        const sidebar = document.querySelector('.left-sidenav');
        const pageWrapper = document.querySelector('.page-wrapper');
        const toggleIcon = document.getElementById('sidebar-toggle-icon');
        const isHidden = localStorage.getItem('sidebarHidden') === 'true';

        if (isHidden) {
            sidebar.style.display = '';
            pageWrapper.style.marginLeft = '';
            localStorage.setItem('sidebarHidden', 'false');
            toggleIcon.classList.replace('fa-bars', 'fa-times');
        } else {
            sidebar.style.display = 'none';
            pageWrapper.style.marginLeft = '0';
            localStorage.setItem('sidebarHidden', 'true');
            toggleIcon.classList.replace('fa-times', 'fa-bars');
        }
    }
</script>

<style>
    .logout-btn:hover i,
    .sidebar-toggle-btn:hover i {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
</style>
