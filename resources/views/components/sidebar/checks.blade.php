{{-- Checks Management Sidebar Component --}}
@can('عرض الشيكات')
<li class="li-main">
    <a href="javascript: void(0);">
        <i data-feather="check-square" style="color:#28a745" class="align-self-center menu-icon"></i>
        <span>{{ __('navigation.checks_management') }}</span>
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse" aria-expanded="false">
        {{-- أوراق القبض --}}
        <li class="nav-item">
            <a class="nav-link font-family-cairo fw-bold" href="{{ route('checks.incoming') }}">
                <i class="fas fa-arrow-circle-down" style="color:#28a745"></i> {{ __('navigation.incoming_checks') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-family-cairo fw-bold" href="{{ route('checks.incoming.create') }}">
                <i class="fas fa-plus-circle" style="color:#28a745"></i> إضافة ورقة قبض
            </a>
        </li>

        {{-- فاصل --}}
        <li class="nav-item">
            <hr class="my-2">
        </li>

        {{-- أوراق الدفع --}}
        <li class="nav-item">
            <a class="nav-link font-family-cairo fw-bold" href="{{ route('checks.outgoing') }}">
                <i class="fas fa-arrow-circle-up" style="color:#dc3545"></i> {{ __('navigation.outgoing_checks') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-family-cairo fw-bold" href="{{ route('checks.outgoing.create') }}">
                <i class="fas fa-plus-circle" style="color:#dc3545"></i> إضافة ورقة دفع
            </a>
        </li>

        {{-- فاصل --}}
        <li class="nav-item">
            <hr class="my-2">
        </li>

        {{-- لوحة التحكم --}}
        <li class="nav-item">
            <a class="nav-link font-family-cairo fw-bold" href="{{ route('checks.dashboard') }}">
                <i class="fas fa-chart-line" style="color:#667eea"></i> {{ __('navigation.checks_dashboard') }}
            </a>
        </li>
    </ul>
</li>
@endcan
