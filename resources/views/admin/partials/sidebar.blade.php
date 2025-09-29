<!-- Left Sidenav -->
<div class="left-sidenav">
    <style>
        /* .menu-content a {
            color: black !important;
            background-color: rgb(250, 250, 250) !important;
            border: rgb(143, 143, 143) solid 1px;
        }

        .menu-content li {
            margin: 1px !important;
        }

        .menu-content a:hover {
            color: white !important;
            background-color:rgb(97, 139, 255) !important;
            padding: 3px !important;

        } */

        /* .menu-content i{
            font-size:40px !important;
        } */
    </style>
    <div class="menu-content h-100" data-simplebar>
        <ul class="metismenu left-sidenav-menu">

            <li class="menu-label my-2"><a href="{{ route('home') }}">{{ config('public_settings.campany_name') }}</a>
            </li>

            <li class="nav-item border-bottom pb-1 mb-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link d-flex align-items-center gap-2 font-family-cairo fw-bold">
                    <i data-feather="home" style="color:#4e73df" class="menu-icon"></i>
                    {{ __('navigation.home') }}
                </a>

            </li>
            @php
                use App\Helpers\ModuleHelper;
                $currentModule = ModuleHelper::getCurrentModule();
            @endphp

            @if ($currentModule)
                @include("components.sidebar.{$currentModule}")
            @else
                @include('components.sidebar.accounts')
            @endif

            @if (!$currentModule)
                @include('components.sidebar.items')
                @include('components.sidebar.discounts')
                @include('components.sidebar.manufacturing')
                @include('components.sidebar.permissions')
                @include('components.sidebar.crm')
                @include('components.sidebar.sales-invoices')
                @include('components.sidebar.purchases-invoices')
                @include('components.sidebar.inventory-invoices')
                @include('components.sidebar.vouchers')
                @include('components.sidebar.transfers')
                @include('components.sidebar.merit-vouchers')
                @include('components.sidebar.contract-journals')
                @include('components.sidebar.multi-vouchers')
                @include('components.sidebar.journals')
                @include('components.sidebar.projects')
                @include('components.sidebar.departments')
                @include('components.sidebar.settings')
                @include('components.sidebar.rentals')
                @include('components.sidebar.service')
                @include('components.sidebar.shipping')
                @include('components.sidebar.POS')
                @include('components.sidebar.daily_progress')
                @include('components.sidebar.inquiries')
            @endif

        </ul>
    </div>
</div>
