<li class="li-main">
    <a href="javascript: void(0);">
        <i data-feather="shopping-cart" class="align-self-center menu-icon"></i>
        <span>{{ __('sidebar.pos') }}</span>
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse" aria-expanded="false">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('pos.index') }}">
                <i class="las la-cash-register"></i>{{ __('navigation.point_of_sale') }}
            </a>
        </li>
    </ul>
</li>
