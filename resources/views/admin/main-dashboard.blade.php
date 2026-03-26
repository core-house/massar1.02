<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        {{ config('app.name', 'Massar') }} | {{ __('dashboard.dashboard') }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="user-id" content="{{ auth()->id() }}" />

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Bootstrap & Core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Masar Themes -->
    <link rel="stylesheet" href="{{ asset('css/themes/masar-themes.css') }}" />

    <!-- Dashboard Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard-main.css') }}" />

    <!-- Lucide Icons CDN -->
    <script src="{{ asset('assets/js/lucide.js') }}"></script>

    @livewireStyles
</head>

<body class="theme-neumorphism-lite">
    <script>
        (function() {
            var k = "masar_theme";
            var v;
            try {
                v = localStorage.getItem(k);
            } catch (e) {
                v = null;
            }
            var t =
                v && ["classic", "mint-green", "dark", "monokai"].indexOf(v) !==
                -1 ?
                v :
                "classic";
            document.body.classList.add("theme-" + t);
        })();
    </script>
    <!-- Animated Doodles Background - Geometric Shapes, Currency & ERP Icons -->
    <div class="doodles-container">
        <!-- Dollar Sign Icon -->
        <svg class="doodle doodle-1" width="80" height="80" viewBox="0 0 80 80"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="30" stroke="#239d77" stroke-width="2.5" fill="none"
                opacity="0.25" />
            <text x="40" y="50" font-family="Arial, sans-serif" font-size="40" font-weight="bold" fill="#239d77"
                opacity="0.3" text-anchor="middle">
                $
            </text>
        </svg>

        <!-- Chart/Graph Icon -->
        <svg class="doodle doodle-2" width="100" height="100" viewBox="0 0 100 100"
            xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="60" width="15" height="30" fill="#34d3a3" opacity="0.25" />
            <rect x="40" y="40" width="15" height="50" fill="#239d77" opacity="0.25" />
            <rect x="60" y="30" width="15" height="60" fill="#2ba88a" opacity="0.25" />
            <line x1="15" y1="80" x2="85" y2="80" stroke="#239d77" stroke-width="2"
                opacity="0.2" />
            <line x1="15" y1="80" x2="15" y2="15" stroke="#239d77" stroke-width="2"
                opacity="0.2" />
        </svg>

        <!-- Box/Inventory Icon -->
        <svg class="doodle doodle-3" width="90" height="90" viewBox="0 0 90 90"
            xmlns="http://www.w3.org/2000/svg">
            <rect x="25" y="25" width="40" height="40" stroke="#34d3a3" stroke-width="2.5" fill="none"
                opacity="0.25" />
            <rect x="30" y="30" width="30" height="30" stroke="#239d77" stroke-width="2" fill="none"
                opacity="0.2" />
            <line x1="25" y1="25" x2="35" y2="15" stroke="#239d77" stroke-width="2"
                opacity="0.2" />
            <line x1="65" y1="25" x2="75" y2="15" stroke="#239d77" stroke-width="2"
                opacity="0.2" />
            <line x1="35" y1="15" x2="75" y2="15" stroke="#239d77" stroke-width="2"
                opacity="0.2" />
        </svg>

        <!-- Euro Sign -->
        <svg class="doodle doodle-4" width="70" height="70" viewBox="0 0 70 70"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="35" cy="35" r="25" stroke="#2ba88a" stroke-width="2.5" fill="none"
                opacity="0.25" />
            <text x="35" y="45" font-family="Arial, sans-serif" font-size="35" font-weight="bold" fill="#2ba88a"
                opacity="0.3" text-anchor="middle">
                €
            </text>
        </svg>

        <!-- Database Icon -->
        <svg class="doodle doodle-5" width="110" height="110" viewBox="0 0 110 110"
            xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="55" cy="30" rx="35" ry="12" stroke="#34d3a3"
                stroke-width="2.5" fill="none" opacity="0.25" />
            <ellipse cx="55" cy="55" rx="35" ry="12" stroke="#239d77"
                stroke-width="2.5" fill="none" opacity="0.25" />
            <ellipse cx="55" cy="80" rx="35" ry="12" stroke="#2ba88a"
                stroke-width="2.5" fill="none" opacity="0.25" />
            <line x1="20" y1="30" x2="20" y2="80" stroke="#239d77" stroke-width="2.5"
                opacity="0.2" />
            <line x1="90" y1="30" x2="90" y2="80" stroke="#239d77" stroke-width="2.5"
                opacity="0.2" />
        </svg>

        <!-- File/Document Icon -->
        <svg class="doodle doodle-6" width="85" height="85" viewBox="0 0 85 85"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M25 20 L25 65 L60 65 L60 35 L45 35 L45 20 Z" stroke="#239d77" stroke-width="2.5" fill="none"
                opacity="0.25" />
            <line x1="45" y1="20" x2="60" y2="35" stroke="#239d77" stroke-width="2.5"
                opacity="0.25" />
            <line x1="30" y1="40" x2="55" y2="40" stroke="#34d3a3" stroke-width="2"
                opacity="0.2" />
            <line x1="30" y1="50" x2="55" y2="50" stroke="#34d3a3" stroke-width="2"
                opacity="0.2" />
        </svg>

        <!-- Geometric Shapes: Triangle -->
        <svg class="doodle doodle-7" width="75" height="75" viewBox="0 0 75 75"
            xmlns="http://www.w3.org/2000/svg">
            <polygon points="37.5,15 60,55 15,55" stroke="#34d3a3" stroke-width="2.5" fill="none"
                opacity="0.25" />
            <circle cx="37.5" cy="40" r="8" fill="#239d77" opacity="0.2" />
        </svg>

        <!-- Geometric Shapes: Hexagon -->
        <svg class="doodle doodle-8" width="95" height="95" viewBox="0 0 95 95"
            xmlns="http://www.w3.org/2000/svg">
            <polygon points="47.5,15 70,30 70,55 47.5,70 25,55 25,30" stroke="#2ba88a" stroke-width="2.5"
                fill="none" opacity="0.25" />
            <circle cx="47.5" cy="42.5" r="12" stroke="#239d77" stroke-width="2" fill="none"
                opacity="0.2" />
        </svg>

        <!-- Coin/Circle with $ -->
        <svg class="doodle doodle-9" width="65" height="65" viewBox="0 0 65 65"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="32.5" cy="32.5" r="25" stroke="#239d77" stroke-width="2.5" fill="#239d77"
                opacity="0.15" />
            <text x="32.5" y="42" font-family="Arial, sans-serif" font-size="30" font-weight="bold" fill="#239d77"
                opacity="0.4" text-anchor="middle">
                $
            </text>
        </svg>

        <!-- Square Grid -->
        <svg class="doodle doodle-10" width="100" height="100" viewBox="0 0 100 100"
            xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="20" width="20" height="20" stroke="#34d3a3" stroke-width="2" fill="none"
                opacity="0.25" />
            <rect x="50" y="20" width="20" height="20" stroke="#239d77" stroke-width="2" fill="none"
                opacity="0.25" />
            <rect x="20" y="50" width="20" height="20" stroke="#2ba88a" stroke-width="2" fill="none"
                opacity="0.25" />
            <rect x="50" y="50" width="20" height="20" stroke="#34d3a3" stroke-width="2" fill="none"
                opacity="0.25" />
        </svg>
    </div>

    <!-- Navbar Section - نفس الـ topbar في باقي المشروع -->
    <div class="topbar">
        <nav class="navbar-custom d-flex justify-content-between align-items-center">
            <ul class="list-unstyled topbar-nav mb-0 d-flex align-items-center">
                <x-notifications::notifications />

                <!-- مبدل اللغة -->
                <li class="me-3">@livewire('language-switcher')</li>

                {{-- Theme switcher dropdown --}}
                <li class="dropdown me-3" data-masar-theme-dropdown>
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false" title="{{ __('Theme') }}"
                        style="color: #34d3a3">
                        <i class="fas fa-palette fa-2x" style="color: #34d3a3"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#" data-masar-theme="classic"><i
                                class="fas fa-palette me-1"></i> Classic
                            (Bootstrap)</a>
                        <a class="dropdown-item" href="#" data-masar-theme="mint-green"><i
                                class="fas fa-leaf me-1"></i> Mint Green</a>
                        <a class="dropdown-item" href="#" data-masar-theme="dark"><i
                                class="fas fa-moon me-1"></i> Dark Mode</a>
                        <a class="dropdown-item" href="#" data-masar-theme="monokai"><i
                                class="fas fa-code me-1"></i> Monokai</a>
                    </div>
                </li>

                @can('view Settings Control')
                    <li>
                        <a title="{{ __('navigation.users') }}" href="{{ route('mysettings.index') }}"
                            class="nav-link transition-base" style="color: #34d3a3">
                            <i class="fas fa-cog fa-2x" style="color: #34d3a3"></i>
                        </a>
                    </li>
                @endcan
                <li>
                    <button type="button" class="btn btn-lg transition-base logout-btn"
                        title="{{ __('navigation.logout') }}" onclick="confirmLogout()"
                        style="
                                background: none;
                                border: none;
                                color: #34d3a3;
                                cursor: pointer;
                            ">
                        <i class="fas fa-sign-out-alt fa-2x" style="color: #34d3a3"></i>
                    </button>

                    {{-- الفورم المخفي --}}
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none">
                        @csrf
                    </form>
                </li>
            </ul>

            <!-- Search Bar في المنتصف -->
            <div class="d-flex align-items-center mx-4">
                <div class="search-container-navbar">
                    <i data-lucide="search" class="search-icon-navbar"></i>
                    <input type="text" id="searchInput" class="search-input-navbar"
                        placeholder="{{ __('dashboard.search_placeholder') }}" />
                    <span class="search-count-navbar" id="searchCount"></span>
                </div>
            </div>

            {{-- <ul class="list-unstyled topbar-nav mb-0 d-flex align-items-center order-first">
                <li>
                    <a title="help" href="https://www.updates.elhadeerp.com" class="nav-link transition-base"
                        target="_blank" style="color: #34d3a3">
                        <i class="fas fa-book fa-2x" style="color: #34d3a3"></i>
                    </a>
                </li>
                @can('view Users')
                    <li>
                        <a title="{{ __('navigation.users') }}" href="{{ route('users.index') }}"
                            class="nav-link transition-base" style="color: #34d3a3">
                            <i class="fas fa-user fa-2x" style="color: #34d3a3"></i>
                        </a>
                    </li>
                @endcan

                <li>
                    <a title="{{ __('navigation.reports') }}" href="{{ route('reports.index') }}"
                        class="nav-link transition-base" style="color: #34d3a3">
                        <i class="fas fa-chart-pie fa-2x" style="color: #34d3a3"></i>
                    </a>
                </li>

                <li>
                    <a title="{{ __('Branches') }}" href="{{ route('branches.index') }}"
                        class="nav-link transition-base" style="color: #34d3a3">
                        <i class="fas fa-store fa-2x" style="color: #34d3a3"></i>
                    </a>
                </li>
            </ul> --}}
        </nav>
    </div>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: "هل أنت متأكد؟",
                text: "سيتم تسجيل خروجك من النظام",
                icon: "warning",
                iconColor: "#34d3a3",
                showCancelButton: true,
                confirmButtonColor: "#34d3a3",
                cancelButtonColor: "#d33",
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> نعم، تسجيل الخروج',
                cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: "animated-popup",
                    confirmButton: "btn-confirm-logout",
                    cancelButton: "btn-cancel-logout",
                },
                showClass: {
                    popup: "animate__animated animate__fadeInDown",
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "جاري تسجيل الخروج...",
                        html: '<div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer: 1000,
                    });

                    setTimeout(() => {
                        document.getElementById("logout-form").submit();
                    }, 1000);
                }
            });
        }
    </script>

    <style>
        /* Navbar Styles */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            width: 100%;
        }

        .navbar-custom {
            padding: 0.75rem 1.5rem;
        }

        /* Search Bar في الـ Navbar */
        .search-container-navbar {
            position: relative;
            display: flex;
            align-items: center;
            min-width: 400px;
        }

        .search-icon-navbar {
            position: absolute;
            right: 15px;
            width: 20px;
            height: 20px;
            color: #34d3a3;
            pointer-events: none;
        }

        .search-input-navbar {
            width: 100%;
            padding: 10px 45px 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .search-input-navbar:focus {
            outline: none;
            border-color: #34d3a3;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 211, 163, 0.1);
        }

        .search-count-navbar {
            position: absolute;
            left: 15px;
            font-size: 12px;
            color: #34d3a3;
            font-weight: 600;
            display: none;
        }

        .animated-popup {
            border-radius: 15px !important;
            box-shadow: 0 10px 40px rgba(52, 211, 163, 0.3) !important;
        }

        .btn-confirm-logout {
            border-radius: 8px !important;
            font-weight: bold !important;
            padding: 10px 25px !important;
        }

        .btn-cancel-logout {
            border-radius: 8px !important;
            font-weight: bold !important;
            padding: 10px 25px !important;
        }

        .logout-btn:hover i {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        /* Fix navbar styling */
        .topbar .nav-link {
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar .topbar-nav li {
            list-style: none;
        }

        /* Fix body padding to account for fixed navbar */
        body {
            padding-top: 70px !important;
        }

        /* Fix dashboard container to not overlap with navbar */
        .dashboard-container {
            margin-top: 0 !important;
            padding-top: 20px !important;
            max-width: 100% !important;
            width: 100% !important;
            padding-left: 1.% !important;
            padding-right: 1.5rem !important;
        }

        /* Hide old header section */
        .header-section {
            display: none !important;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .search-container-navbar {
                min-width: 250px;
            }
        }

        @media (max-width: 768px) {
            .search-container-navbar {
                display: none;
            }
        }
    </style>

    <div class="dashboard-container">

        @php
            $subscriptionEnd = tenant()->getSubscriptionEndDate();
            $daysRemaining = null;
            if ($subscriptionEnd) {
                $daysRemaining = (int) now()->startOfDay()->diffInDays($subscriptionEnd->startOfDay(), false);
            }
        @endphp

        @if ($daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7)
            <div class="alert alert-warning alert-dismissible fade show mx-4 mb-4 shadow-sm border-0 rounded-3"
                role="alert" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-lucide="alert-triangle" style="width: 32px; height: 32px; color: #856404;"></i>
                    </div>
                    <div class="flex-grow-1 {{ app()->getLocale() === 'ar' ? 'ms-3' : 'me-3' }}">
                        <strong class="d-block mb-1">{{ __('dashboard.subscription_warning_title') }}</strong>
                        <span>
                            {{ __('dashboard.subscription_warning_body', ['days' => $daysRemaining, 'date' => \Carbon\Carbon::parse($subscriptionEnd)->format('Y-m-d')]) }}
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="{{ app()->getLocale() === 'ar' ? 'left: 1rem; right: auto;' : '' }}"></button>
            </div>
        @endif

        <!-- كروت الإحصائيات -->
        <div class="stats-cards-section">
            <div class="row g-3 stats-cards-row">
                <!-- كرت العملاء -->
                <div class="col-lg-4 col-md-4 stats-card-col">
                    <div class="card border-0 shadow-lg h-100 stats-card stats-card-clients">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="stats-card-content">
                                    <p class="stats-card-label">
                                        {{ __('dashboard.total_clients') }}
                                    </p>
                                    <h2 class="stats-card-value">
                                        {{ number_format($totalClients ?? 0) }}
                                    </h2>
                                    <p class="stats-card-subtitle">
                                        <i data-lucide="trending-up"></i>
                                        {{ __('dashboard.total_clients_label') }}
                                    </p>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i data-lucide="users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-decoration card-decoration-top"></div>
                        <div class="card-decoration card-decoration-bottom"></div>
                    </div>
                </div>

                <!-- كرت مرات الدخول -->
                <div class="col-lg-4 col-md-4 stats-card-col">
                    <div class="card border-0 shadow-lg h-100 stats-card stats-card-logins">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="stats-card-content">
                                    <p class="stats-card-label">
                                        {{ __('dashboard.total_logins') }}
                                    </p>
                                    <h2 class="stats-card-value">
                                        {{ number_format($totalLogins ?? 0) }}
                                    </h2>
                                    <p class="stats-card-subtitle">
                                        <i data-lucide="activity"></i>
                                        {{ __('dashboard.total_sessions') }}
                                    </p>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i data-lucide="log-in"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-decoration card-decoration-top"></div>
                        <div class="card-decoration card-decoration-bottom"></div>
                    </div>
                </div>

                <!-- كرت المبيعات اليوم -->
                <div class="col-lg-4 col-md-4 stats-card-col">
                    <div class="card border-0 shadow-lg h-100 stats-card stats-card-sales">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="stats-card-content">
                                    <p class="stats-card-label">
                                        {{ __('dashboard.today_sales') }}
                                    </p>
                                    <h2 class="stats-card-value">
                                        {{ number_format($todaySales ?? 0, 2) }}
                                    </h2>
                                    <p class="stats-card-subtitle">
                                        <i data-lucide="dollar-sign"></i>
                                        {{ __('dashboard.currency') }}
                                    </p>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i data-lucide="trending-up"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-decoration card-decoration-top"></div>
                        <div class="card-decoration card-decoration-bottom"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أيقونات البرنامج - مجموعات -->
        <div class="apps-groups-section" id="appsGroupsSection">
            {{-- ===== المجموعة 1: العمليات الأساسية ===== --}}
            <div class="module-group" data-group="core">
                <div class="module-group-header module-group-header-blue">
                    <i data-lucide="layout-grid"></i>
                    <span>{{ __('dashboard.group_core') }}</span>
                    <small>{{ __('dashboard.group_core_desc') }}</small>
                </div>
                <div class="module-group-icons">
                    {{-- البيانات الاساسيه --}}
                    @if (tenant()->hasModule('accounts'))
                        @canany([
                            'view Clients',
                            'view Suppliers',
                            'view Funds',
                            'view Banks',
                            'view Employees',
                            'view warhouses',
                            'view
                            Expenses',
                            'view Revenues',
                            'view various_creditors',
                            'view various_debtors',
                            'view partners',
                            'view
                            current_partners',
                            'view assets',
                            'view rentables',
                            'view check-portfolios-incoming',
                            'view
                            basicData-statistics',
                            ])
                            <a href="{{ route('accounts.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                                title="{{ __('dashboard.master_data') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="chart-bar-increasing"></i>
                                </div>
                                <p>{{ __('dashboard.master_data') }}</p>
                            </a>
                        @endcanany
                    @endif
                    {{-- الاصناف --}}
                    @if (tenant()->hasModule('inventory'))
                        @canany([
                            'view items',
                            'view units',
                            'view prices',
                            'view notes-names',
                            'view varibals',
                            'view
                            varibalsValues',
                            ])
                            <a href="{{ route('items.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                                title="{{ __('dashboard.items') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="boxes"></i>
                                </div>
                                <p>{{ __('dashboard.items') }}</p>
                            </a>
                        @endcanany
                    @endif
                    {{-- الاعدادات --}}
                    @can('view settings')
                        <a href="{{ route('mysettings.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                            title="{{ __('dashboard.settings') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="settings"></i>
                            </div>
                            <p>{{ __('dashboard.settings') }}</p>
                        </a>
                    @endcan
                    {{-- الصلاحيات --}}
                    @canany([
                        'view roles',
                        'view branches',
                        'view settings',
                        'view login-history',
                        'view active-sessions',
                        'view
                        activity-logs',
                        ])
                        <a href="{{ route('users.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                            title="{{ __('dashboard.permissions') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="key"></i>
                            </div>
                            <p>{{ __('dashboard.permissions') }}</p>
                        </a>
                    @endcanany
                </div>
            </div>

            {{-- ===== المجموعة 2: دورة المبيعات ===== --}}
            <div class="module-group" data-group="sales">
                <div class="module-group-header module-group-header-orange">
                    <i data-lucide="trending-up"></i>
                    <span>{{ __('dashboard.group_sales') }}</span>
                    <small>{{ __('dashboard.group_sales_desc') }}</small>
                </div>
                <div class="module-group-icons">
                    {{-- crm --}}
                    @if (tenant()->hasModule('crm'))
                        @canany(['view CRM', 'view CRM Statistics'])
                            <a href="{{ route('statistics.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.crm') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="user-cog"></i>
                                </div>
                                <p>{{ __('dashboard.crm') }}</p>
                            </a>
                        @endcanany
                    @endif
                    {{-- المبيعات --}}
                    @if (tenant()->hasModule('invoices'))
                        @can('view Sales Invoice')
                            <a href="{{ route('invoices.index', ['type' => 10]) }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.sales') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="trending-up"></i>
                                </div>
                                <p>{{ __('dashboard.sales') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- pos --}}
                    @if (tenant()->hasModule('pos'))
                        @can('view POS System')
                            <a href="{{ route('pos.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                                title="{{ __('dashboard.pos') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="shopping-cart"></i>
                                </div>
                                <p>{{ __('dashboard.pos') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- ادارة الدفعات --}}
                    @if (tenant()->hasModule('installments'))
                        @can('view Installment Plans')
                            <a href="{{ route('installments.plans.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.installments') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="tag"></i>
                                </div>
                                <p>{{ __('dashboard.installments') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- المهام والأنشطة --}}
                    @can('view Tasks')
                        <a href="{{ route('tasks.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                            title="{{ __('dashboard.tasks_activities') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="check-square"></i>
                            </div>
                            <p>{{ __('dashboard.tasks_activities') }}</p>
                        </a>
                    @endcan
                    {{-- أدارة الشحن --}}
                    @if (tenant()->hasModule('shipping'))
                        @can('view Orders')
                            <a href="{{ route('orders.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                                title="{{ __('dashboard.shipping') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="truck"></i>
                                </div>
                                <p>{{ __('dashboard.shipping') }}</p>
                            </a>
                        @endcan
                    @endif
                </div>
            </div>

            {{-- ===== المجموعة 3: المشتريات والمخازن ===== --}}
            <div class="module-group" data-group="logistics">
                <div class="module-group-header module-group-header-green">
                    <i data-lucide="package"></i>
                    <span>{{ __('dashboard.group_logistics') }}</span>
                    <small>{{ __('dashboard.group_logistics_desc') }}</small>
                </div>
                <div class="module-group-icons">
                    {{-- المشتريات --}}
                    @if (tenant()->hasModule('invoices'))
                        @can('view Purchase Invoice')
                            <a href="{{ route('invoices.index', ['type' => 11]) }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.purchases') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="shopping-bag"></i>
                                </div>
                                <p>{{ __('dashboard.purchases') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- ادارة المخزون --}}
                    @if (tenant()->hasModule('invoices'))
                        @canany([
                            'view Inventory-Management',
                            'view Damaged
                            Goods Invoice',
                            'view Dispatch Order',
                            'view Addition
                            Order',
                            'view Store-to-Store Transfer',
                            ])
                            <a href="{{ route('invoices.index', ['type' => 18]) }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.inventory') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="package"></i>
                                </div>
                                <p>{{ __('dashboard.inventory') }}</p>
                            </a>
                        @endcanany
                    @endif
                </div>
            </div>

            {{-- ===== المجموعة 4: الموديولات المالية ===== --}}
            <div class="module-group" data-group="finance">
                <div class="module-group-header module-group-header-purple">
                    <i data-lucide="landmark"></i>
                    <span>{{ __('dashboard.group_finance') }}</span>
                    <small>{{ __('dashboard.group_finance_desc') }}</small>
                </div>
                <div class="module-group-icons">
                    {{-- ادارة الحسابات --}}
                    @if (tenant()->hasModule('accounts'))
                        @can('view journals')
                            <a href="{{ route('journals.index', ['type' => 'basic_journal']) }}"
                                class="app-icon-large icon-bg-green" target="_blank"
                                title="{{ __('dashboard.accounts') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="file-text"></i>
                                </div>
                                <p>{{ __('dashboard.accounts') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- ادارة الشيكات --}}
                    @if (tenant()->hasModule('checks'))
                        @can('view Checks')
                            <a href="{{ route('checks.incoming') }}" class="app-icon-large icon-bg-green"
                                style="position: relative" target="_blank" title="{{ __('dashboard.checks') }}">
                                <span
                                    style="
                                    position: absolute;
                                    top: 5px;
                                    left: 5px;
                                    background: #ff4757;
                                    color: white;
                                    padding: 2px 6px;
                                    border-radius: 8px;
                                    font-size: 0.65rem;
                                    font-weight: 600;
                                    z-index: 10;
                                ">{{ __('dashboard.new') }}</span>
                                <div class="icon-wrapper">
                                    <i data-lucide="file-check-2"></i>
                                </div>
                                <p>{{ __('dashboard.checks') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- ادارة المصروفات --}}
                    @if (tenant()->hasModule('accounts'))
                        @can('view Expenses-Management')
                            <a href="{{ route('expenses.dashboard') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.expenses') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="credit-card"></i>
                                </div>
                                <p>{{ __('dashboard.expenses') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- السندات الماليه --}}
                    @canany([
                        'view receipt vouchers',
                        'view payment
                        vouchers',
                        'view exp-payment',
                        ])
                        <a href="{{ route('vouchers.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                            title="{{ __('dashboard.vouchers') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="receipt"></i>
                            </div>
                            <p>{{ __('dashboard.vouchers') }}</p>
                        </a>
                    @endcanany
                    {{-- التحويلات النقديه --}}
                    @can('view transfers')
                        <a href="{{ route('transfers.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                            title="{{ __('dashboard.transfers') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="arrow-left-right"></i>
                            </div>
                            <p>{{ __('dashboard.transfers') }}</p>
                        </a>
                    @endcan
                    {{-- عمليات الاصول --}}
                    @if (tenant()->hasModule('depreciation'))
                        <a href="{{ route('depreciation.index') }}" class="app-icon-large icon-bg-green"
                            target="_blank" title="{{ __('dashboard.assets') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="building-2"></i>
                            </div>
                            <p>{{ __('dashboard.assets') }}</p>
                        </a>
                    @endif
                </div>
            </div>

            {{-- ===== المجموعة 5: الإدارة والرقابة ===== --}}
            <div class="module-group" data-group="management">
                <div class="module-group-header module-group-header-cyan">
                    <i data-lucide="bar-chart-2"></i>
                    <span>{{ __('dashboard.group_management') }}</span>
                    <small>{{ __('dashboard.group_management_desc') }}</small>
                </div>
                <div class="module-group-icons">
                    {{-- التقارير --}}
                    @canany([
                        'view DailyWorkAnalysis',
                        'view
                        Chart-of-Accounts',
                        'view balance-sheet',
                        'view
                        Profit-Loss',
                        'view Sales-Reports',
                        'view
                        Purchasing-Reports',
                        'view Inventory-Reports',
                        'view
                        Expenses-Reports',
                        ])
                        <a href="{{ route('reports.index') }}" class="app-icon-large icon-bg-green" target="_blank"
                            title="{{ __('dashboard.reports') }}">
                            <div class="icon-wrapper">
                                <i data-lucide="file-bar-chart"></i>
                            </div>
                            <p>{{ __('dashboard.reports') }}</p>
                        </a>
                    @endcanany
                    {{-- التقدم اليومي --}}
                    @if (tenant()->hasModule('daily_progress'))
                        @canany([
                            'view progress-recyclebin',
                            'view
                            progress-project-types',
                            'view
                            progress-project-templates',
                            'view
                            progress-item-statuses',
                            'view progress-work-items',
                            'view
                            progress-work-item-categories',
                            'view
                            daily-progress',
                            'view progress-issues',
                            'view
                            progress-projects',
                            'view progress-dashboard',
                            ])
                            @if (Route::has('progress.dashboard'))
                                <a href="{{ route('progress.dashboard') }}" class="app-icon-large icon-bg-green"
                                    target="_blank" title="{{ __('dashboard.daily_progress') }}">
                                    <div class="icon-wrapper">
                                        <i data-lucide="bar-chart-3"></i>
                                    </div>
                                    <p>{{ __('dashboard.daily_progress') }}</p>
                                </a>
                            @endif
                        @endcanany
                    @endif
                    {{-- الموارد البشريه --}}
                    @if (tenant()->hasModule('hr'))
                        @can('view Employees')
                            <a href="{{ route('employees.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.hr') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="users"></i>
                                </div>
                                <p>{{ __('dashboard.hr') }}</p>
                            </a>
                        @endcan
                        {{-- بصمة الموبايل --}}
                        @can('view Mobile-fingerprint')
                            <a href="{{ route('mobile.employee-login') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.mobile_fingerprint') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="fingerprint"></i>
                                </div>
                                <p>{{ __('dashboard.mobile_fingerprint') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- إدارة الأسطول --}}
                    @if (tenant()->hasModule('fleet'))
                        @can('view Fleet Dashboard')
                            <a href="{{ route('fleet.dashboard.index') }}?sidebar=fleet"
                                class="app-icon-large icon-bg-green" style="position: relative" target="_blank"
                                title="{{ __('dashboard.fleet') }}">
                                <span
                                    style="
                                    position: absolute;
                                    top: 5px;
                                    left: 5px;
                                    background: #ff4757;
                                    color: white;
                                    padding: 2px 6px;
                                    border-radius: 8px;
                                    font-size: 0.65rem;
                                    font-weight: 600;
                                    z-index: 10;
                                ">{{ __('dashboard.new') }}</span>
                                <div class="icon-wrapper">
                                    <i data-lucide="truck"></i>
                                </div>
                                <p>{{ __('dashboard.fleet') }}</p>
                            </a>
                        @endcan
                    @endif
                </div>
            </div>

            {{-- ===== المجموعة 6: العمليات المتخصصة والإنتاج ===== --}}
            <div class="module-group" data-group="operations">
                <div class="module-group-header module-group-header-pink">
                    <i data-lucide="factory"></i>
                    <span>{{ __('dashboard.group_operations') }}</span>
                    <small>{{ __('dashboard.group_operations_desc') }}</small>
                </div>
                <div class="module-group-icons">
                    {{-- التصنيع --}}
                    @if (tenant()->hasModule('manufacturing'))
                        @can('view Manufacturing Invoices')
                            <a href="{{ route('manufacturing.create') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.manufacturing') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="factory"></i>
                                </div>
                                <p>{{ __('dashboard.manufacturing') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- إدارة الجودة --}}
                    @if (tenant()->hasModule('quality'))
                        @canany([
                            'view quality',
                            'view inspections',
                            'view
                            standards',
                            'view ncr',
                            'view capa',
                            'view batches',
                            'view rateSuppliers',
                            'view certificates',
                            'view
                            audits',
                            ])
                            <a href="{{ route('quality.dashboard') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.quality') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="award"></i>
                                </div>
                                <p>{{ __('dashboard.quality') }}</p>
                            </a>
                        @endcanany
                    @endif
                    {{-- ادارة المستأجرات --}}
                    @if (tenant()->hasModule('rentals'))
                        @can('view Buildings')
                            <a href="{{ route('rentals.buildings.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.rentals') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="building"></i>
                                </div>
                                <p>{{ __('dashboard.rentals') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- المشاريع --}}
                    @if (tenant()->hasModule('projects'))
                        @can('view Projects')
                            @if (Route::has('projects.index'))
                                <a href="{{ route('projects.index') }}" class="app-icon-large icon-bg-green"
                                    target="_blank" title="{{ __('dashboard.projects') }}">
                                    <div class="icon-wrapper">
                                        <i data-lucide="kanban"></i>
                                    </div>
                                    <p>{{ __('dashboard.projects') }}</p>
                                </a>
                            @endif
                        @endcan
                    @endif
                    {{-- الصيانه --}}
                    @if (tenant()->hasModule('maintenance'))
                        @canany(['view Service Types', 'view Maintenances', 'view Periodic Maintenance', 'view
                            Maintenance'])
                            <a href="{{ route('service.types.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.maintenance') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="wrench"></i>
                                </div>
                                <p>{{ __('dashboard.maintenance') }}</p>
                            </a>
                        @endcanany
                    @endif
                    {{-- ادارة الموارد --}}
                    @if (tenant()->hasModule('myResources'))
                        @can('view MyResources')
                            <a href="{{ route('myresources.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.resources') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="cog"></i>
                                </div>
                                <p>{{ __('dashboard.resources') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- Inquiries --}}
                    @if (tenant()->hasModule('inquiries'))
                        @can('view Inquiries')
                            <a href="{{ route('inquiries.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank" title="{{ __('dashboard.inquiries') }}">
                                <div class="icon-wrapper">
                                    <i data-lucide="layers"></i>
                                </div>
                                <p>{{ __('dashboard.inquiries') }}</p>
                            </a>
                        @endcan
                    @endif
                    {{-- الوثائق والمستندات --}}
                    @canany(['view Documents', 'view Document Categories'])
                        {{-- <a
                            href="{{ route('documents.index') }}"
                            class="app-icon-large icon-bg-green"
                            target="_blank"
                            title="{{ __('dashboard.documents') }}"
                        >
                            <div class="icon-wrapper">
                                <i data-lucide="folder-open"></i>
                            </div>
                            <p>{{ __("dashboard.documents") }}</p>
                        </a> --}}
                    @endcanany
                </div>
            </div>
        </div>
        <!-- end apps-groups-section -->

        {{-- hidden container للـ search --}}
        <div class="apps-icons-row d-none" id="searchResultsRow">
            <div class="d-flex" id="searchResultsGrid">
                {{-- يتم ملؤه ديناميكياً عبر JS --}}

                {{-- الاصناف --}}
                <div class="app-icon-group">
                    @canany([
                        'view items',
                        'view units',
                        'view prices',
                        'view notes-names',
                        'view varibals',
                        'view
                        varibalsValues',
                        ])
                        <a href="{{ route('items.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                            <div class="icon-wrapper">
                                <i data-lucide="boxes"></i>
                            </div>
                            <p>{{ __('dashboard.items') }}</p>
                        </a>
                    @endcanany
                </div>

                {{-- الصلاحيات --}}
                <div class="app-icon-group">
                    @canany([
                        'view roles',
                        'view branches',
                        'view settings',
                        'view login-history',
                        'view active-sessions',
                        'view
                        activity-logs',
                        ])
                        <a href="{{ route('users.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                            <div class="icon-wrapper">
                                <i data-lucide="key"></i>
                            </div>
                            <p>{{ __('dashboard.permissions') }}</p>
                        </a>
                    @endcanany
                </div>

                {{-- الاعدادات --}}
                <div class="app-icon-group">
                    @can('view settings')
                        <a href="{{ route('mysettings.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                            <div class="icon-wrapper">
                                <i data-lucide="settings"></i>
                            </div>
                            <p>{{ __('dashboard.settings') }}</p>
                        </a>
                    @endcan
                </div>

                {{-- التقارير --}}
                <div class="app-icon-group">
                    @canany([
                        'view DailyWorkAnalysis',
                        'view
                        Chart-of-Accounts',
                        'view balance-sheet',
                        'view
                        Profit-Loss',
                        'view Sales-Reports',
                        'view
                        Purchasing-Reports',
                        'view Inventory-Reports',
                        'view
                        Expenses-Reports',
                        ])
                        <a href="{{ route('reports.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                            <div class="icon-wrapper">
                                <i data-lucide="file-bar-chart"></i>
                            </div>
                            <p>{{ __('dashboard.reports') }}</p>
                        </a>
                    @endcanany
                </div>

                {{-- crm --}}
                <div class="app-icon-group">
                    @canany(['view CRM', 'view CRM Statistics'])
                        <a href="{{ route('statistics.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                            <div class="icon-wrapper">
                                <i data-lucide="user-cog"></i>
                            </div>
                            <p>{{ __('dashboard.crm') }}</p>
                        </a>
                    @endcanany
                </div>

                {{-- المهام والأنشطة --}}
                @if (tenant()->hasModule('installments') || tenant()->hasModule('crm'))
                    <div class="app-icon-group">
                        @can('view Tasks')
                            <a href="{{ route('tasks.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="check-square"></i>
                                </div>
                                <p>{{ __('dashboard.tasks_activities') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- المبيعات --}}
                @if (tenant()->hasModule('invoices'))
                    <div class="app-icon-group">
                        @can('view Sales Invoice')
                            <a href="{{ route('invoices.index', ['type' => 10]) }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="trending-up"></i>
                                </div>
                                <p>{{ __('dashboard.sales') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- pos --}}
                @if (tenant()->hasModule('pos'))
                    <div class="app-icon-group">
                        @can('view POS System')
                            <a href="{{ route('pos.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="shopping-cart"></i>
                                </div>
                                <p>{{ __('dashboard.pos') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- ادارة المستأجرات --}}
                @if (tenant()->hasModule('rentals'))
                    <div class="app-icon-group">
                        @can('view Buildings')
                            <a href="{{ route('rentals.buildings.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="building"></i>
                                </div>
                                <p>{{ __('dashboard.rentals') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- ادارة الحسابات --}}
                @if (tenant()->hasModule('accounts'))
                    <div class="app-icon-group">
                        @can('view journals')
                            <a href="{{ route('journals.index', ['type' => 'basic_journal']) }}"
                                class="app-icon-large icon-bg-green" target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="file-text"></i>
                                </div>
                                <p>{{ __('dashboard.accounts') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- ادارة المصروفات --}}
                @if (tenant()->hasModule('accounts'))
                    <div class="app-icon-group">
                        @can('view Expenses-Management')
                            <a href="{{ route('expenses.dashboard') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="credit-card"></i>
                                </div>
                                <p>{{ __('dashboard.expenses') }}</p>
                            </a>
                        @endcan
                    </div>

                    {{-- السندات الماليه --}}
                    <div class="app-icon-group">
                        @canany([
                            'view receipt vouchers',
                            'view payment
                            vouchers',
                            'view exp-payment',
                            ])
                            <a href="{{ route('vouchers.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="receipt"></i>
                                </div>
                                <p>{{ __('dashboard.vouchers') }}</p>
                            </a>
                        @endcanany
                    </div>
                @endif

                {{-- التحويلات النقديه --}}
                @if (tenant()->hasModule('accounts'))
                    <div class="app-icon-group">
                        @can('view transfers')
                            <a href="{{ route('transfers.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="arrow-left-right"></i>
                                </div>
                                <p>{{ __('dashboard.transfers') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- ادارة الدفعات --}}
                @if (tenant()->hasModule('installments'))
                    <div class="app-icon-group">
                        @can('view Installment Plans')
                            <a href="{{ route('installments.plans.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="tag"></i>
                                </div>
                                <p>{{ __('dashboard.installments') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- ادارة الشيكات --}}
                @if (tenant()->hasModule('checks'))
                    <div class="app-icon-group">
                        @can('view Checks')
                            <a href="{{ route('checks.incoming') }}" class="app-icon-large icon-bg-green"
                                style="position: relative" target="_blank">
                                <span
                                    style="
                                    position: absolute;
                                    top: 5px;
                                    left: 5px;
                                    background: #ff4757;
                                    color: white;
                                    padding: 2px 6px;
                                    border-radius: 8px;
                                    font-size: 0.65rem;
                                    font-weight: 600;
                                    z-index: 10;
                                ">{{ __('dashboard.new') }}</span>
                                <div class="icon-wrapper">
                                    <i data-lucide="file-check-2"></i>
                                </div>
                                <p>{{ __('dashboard.checks') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- ادارة المخزون --}}
                @if (tenant()->hasModule('invoices'))
                    <div class="app-icon-group">
                        @canany([
                            'view Inventory-Management',
                            'view Damaged
                            Goods Invoice',
                            'view Dispatch Order',
                            'view Addition
                            Order',
                            'view Store-to-Store Transfer',
                            ])
                            <a href="{{ route('invoices.index', ['type' => 18]) }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="package"></i>
                                </div>
                                <p>{{ __('dashboard.inventory') }}</p>
                            </a>
                        @endcanany
                    </div>
                @endif

                {{-- التصنيع --}}
                @if (tenant()->hasModule('manufacturing'))
                    <div class="app-icon-group">
                        @can('view Manufacturing Invoices')
                            <a href="{{ route('manufacturing.create') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="factory"></i>
                                </div>
                                <p>{{ __('dashboard.manufacturing') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- إدارة الجودة --}}
                @if (tenant()->hasModule('quality'))
                    <div class="app-icon-group">
                        @canany([
                            'view quality',
                            'view inspections',
                            'view
                            standards',
                            'view ncr',
                            'view capa',
                            'view batches',
                            'view rateSuppliers',
                            'view certificates',
                            'view
                            audits',
                            ])
                            <a href="{{ route('quality.dashboard') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="award"></i>
                                </div>
                                <p>{{ __('dashboard.quality') }}</p>
                            </a>
                        @endcanany
                    </div>
                @endif

                {{-- المشتريات --}}
                @if (tenant()->hasModule('invoices'))
                    <div class="app-icon-group">
                        @can('view Purchase Invoice')
                            <a href="{{ route('invoices.index', ['type' => 11]) }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="shopping-bag"></i>
                                </div>
                                <p>{{ __('dashboard.purchases') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- الصيانه --}}
                @if (tenant()->hasModule('maintenance'))
                    <div class="app-icon-group">
                        @canany(['view Service Types', 'view Maintenances', 'view Periodic Maintenance', 'view
                            Maintenance'])
                            <a href="{{ route('service.types.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="wrench"></i>
                                </div>
                                <p>{{ __('dashboard.maintenance') }}</p>
                            </a>
                        @endcanany
                    </div>
                @endif

                {{-- إدارة الأسطول --}}
                @if (tenant()->hasModule('fleet'))
                    <div class="app-icon-group">
                        @can('view Fleet Dashboard')
                            <a href="{{ route('fleet.dashboard.index') }}?sidebar=fleet"
                                class="app-icon-large icon-bg-green" style="position: relative" target="_blank">
                                <span
                                    style="
                                    position: absolute;
                                    top: 5px;
                                    left: 5px;
                                    background: #ff4757;
                                    color: white;
                                    padding: 2px 6px;
                                    border-radius: 8px;
                                    font-size: 0.65rem;
                                    font-weight: 600;
                                    z-index: 10;
                                ">{{ __('dashboard.new') }}</span>
                                <div class="icon-wrapper">
                                    <i data-lucide="truck"></i>
                                </div>
                                <p>{{ __('dashboard.fleet') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- المشاريع --}}
                @if (tenant()->hasModule('projects'))
                    <div class="app-icon-group">
                        @can('view Projects')
                            @if (Route::has('projects.index'))
                                <a href="{{ route('projects.index') }}" class="app-icon-large icon-bg-green"
                                    target="_blank">
                                    <div class="icon-wrapper">
                                        <i data-lucide="kanban"></i>
                                    </div>
                                    <p>{{ __('dashboard.projects') }}</p>
                                </a>
                            @endif
                        @endcan
                    </div>
                @endif

                {{-- التقدم اليومي --}}
                @if (tenant()->hasModule('daily_progress'))
                    <div class="app-icon-group">
                        @canany([
                            'view progress-recyclebin',
                            'view
                            progress-project-types',
                            'view
                            progress-project-templates',
                            'view
                            progress-item-statuses',
                            'view progress-work-items',
                            'view
                            progress-work-item-categories',
                            'view daily-progress',
                            'view progress-issues',
                            'view progress-projects',
                            'view
                            progress-dashboard',
                            ])
                            @if (Route::has('progress.dashboard'))
                                <a href="{{ route('progress.dashboard') }}" class="app-icon-large icon-bg-green"
                                    target="_blank">
                                    <div class="icon-wrapper">
                                        <i data-lucide="bar-chart-3"></i>
                                    </div>
                                    <p>{{ __('dashboard.daily_progress') }}</p>
                                </a>
                            @endif
                        @endcanany
                    </div>
                @endif

                {{-- عمليات الاصول --}}
                @if (tenant()->hasModule('depreciation'))
                    <div class="app-icon-group">
                        <a href="{{ route('depreciation.index') }}" class="app-icon-large icon-bg-green"
                            target="_blank">
                            <div class="icon-wrapper">
                                <i data-lucide="building"></i>
                            </div>
                            <p>{{ __('dashboard.assets') }}</p>
                        </a>
                    </div>
                @endif

                {{-- ادارة الموارد --}}
                @if (tenant()->hasModule('myResources'))
                    <div class="app-icon-group">
                        @can('view MyResources')
                            <a href="{{ route('myresources.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="cog"></i>
                                </div>
                                <p>{{ __('dashboard.resources') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- الموارد البشريه --}}
                @if (tenant()->hasModule('hr'))
                    <div class="app-icon-group">
                        @can('view Employees')
                            <a href="{{ route('employees.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="users"></i>
                                </div>
                                <p>{{ __('dashboard.hr') }}</p>
                            </a>
                        @endcan
                    </div>

                    {{-- بصمة الموبايل --}}
                    <div class="app-icon-group">
                        @can('view Mobile-fingerprint')
                            <a href="{{ route('mobile.employee-login') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="fingerprint"></i>
                                </div>
                                <p>{{ __('dashboard.mobile_fingerprint') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- أدارة الشحن --}}
                @if (tenant()->hasModule('shipping'))
                    <div class="app-icon-group">
                        @can('view Orders')
                            <a href="{{ route('orders.index') }}" class="app-icon-large icon-bg-green" target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="truck"></i>
                                </div>
                                <p>{{ __('dashboard.shipping') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- Inquiries --}}
                @if (tenant()->hasModule('inquiries'))
                    <div class="app-icon-group">
                        @can('view Inquiries')
                            <a href="{{ route('inquiries.index') }}" class="app-icon-large icon-bg-green"
                                target="_blank">
                                <div class="icon-wrapper">
                                    <i data-lucide="layers"></i>
                                </div>
                                <p>{{ __('dashboard.inquiries') }}</p>
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- الوثائق والمستندات --}}
                <div class="app-icon-group">
                    @canany(['view Documents', 'view Document Categories'])
                        {{-- <a
                            href="{{ route('documents.index') }}"
                            class="app-icon-large icon-bg-green"
                            target="_blank"
                        >
                            <div class="icon-wrapper">
                                <i data-lucide="folder-open"></i>
                            </div>
                            <p>{{ __("dashboard.documents") }}</p>
                        </a> --}}
                    @endcanany
                </div>

                {{-- المشاريع --}}
            </div>
        </div>

        <!-- Gamification Section -->
        @if (tenant()->hasModule('gamification'))
            <div class="gamification-section mt-5">
                <livewire:gamification::gamification-dashboard />
            </div>
        @endif

        <!-- الجداول (3 في الصف) -->
        <div class="tables-section" style="margin-top: 3rem">
            <div class="row g-4">
                <!-- آخر 5 حسابات -->
                @if (tenant()->hasModule('accounts'))
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem">
                                <h5 class="mb-0 fw-bold tables-section-title">
                                    <i data-lucide="wallet"
                                        style="
                                            width: 20px;
                                            height: 20px;
                                            margin-left: 8px;
                                            vertical-align: middle;
                                        "></i>
                                    {{ __('dashboard.recent_accounts') }}
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{ __('dashboard.code') }}
                                                </th>
                                                <th>
                                                    {{ __('dashboard.name') }}
                                                </th>
                                                <th>
                                                    {{ __('dashboard.number') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentAccounts ?? [] as $account)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $account->code ?? '-' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $account->aname ?? '-' }}
                                                    </td>
                                                    <td>#{{ $account->id }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5">
                                                        {{ __('dashboard.no_data') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- آخر 5 عمليات تسجيل دخول -->
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem">
                            <h5 class="mb-0 fw-bold tables-section-title">
                                <i data-lucide="log-in"
                                    style="
                                            width: 20px;
                                            height: 20px;
                                            margin-left: 8px;
                                            vertical-align: middle;
                                        "></i>
                                {{ __('dashboard.recent_logins') }}
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>
                                                {{ __('dashboard.user') }}
                                            </th>
                                            <th>
                                                {{ __('dashboard.date') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLogins ?? [] as $login)
                                            <tr>
                                                <td>
                                                    <strong>{{ $login->user->name ?? '-' }}</strong>
                                                </td>
                                                <td style="font-size: 0.875rem">
                                                    {{ $login->login_at ? $login->login_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5">
                                                    لا توجد بيانات
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات المبيعات -->
                @if (tenant()->hasModule('invoices'))
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem">
                                <h5 class="mb-0 fw-bold tables-section-title">
                                    <i data-lucide="trending-up"
                                        style="
                                            width: 20px;
                                            height: 20px;
                                            margin-left: 8px;
                                            vertical-align: middle;
                                        "></i>
                                    {{ __('dashboard.sales_statistics') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-3">
                                    <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                        <span class="sales-stats-label">{{ __('dashboard.last_invoice') }}</span>
                                        <span class="sales-stats-value">
                                            {{ $salesStats['last_invoice'] ? '#' . $salesStats['last_invoice']->pro_id . ' - ' . number_format($salesStats['last_invoice']->fat_net ?? 0, 2) . ' ' . __('dashboard.currency') : '-' }}
                                        </span>
                                    </div>
                                    <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                        <span class="sales-stats-label">{{ __('dashboard.today') }}</span>
                                        <span class="sales-stats-value">
                                            {{ number_format($salesStats['today'] ?? 0, 2) }}
                                            {{ __('dashboard.currency') }}
                                        </span>
                                    </div>
                                    <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                        <span class="sales-stats-label">{{ __('dashboard.last_week') }}</span>
                                        <span class="sales-stats-value">
                                            {{ number_format($salesStats['last_week'] ?? 0, 2) }}
                                            {{ __('dashboard.currency') }}
                                        </span>
                                    </div>
                                    <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                        <span class="sales-stats-label">{{ __('dashboard.last_month') }}</span>
                                        <span class="sales-stats-value">
                                            {{ number_format($salesStats['last_month'] ?? 0, 2) }}
                                            {{ __('dashboard.currency') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- آخر 5 أصناف -->
                @if (tenant()->hasModule('invoices'))
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem">
                                <h5 class="mb-0 fw-bold tables-section-title">
                                    <i data-lucide="package"
                                        style="
                                            width: 20px;
                                            height: 20px;
                                            margin-left: 8px;
                                            vertical-align: middle;
                                        "></i>
                                    {{ __('dashboard.recent_items') }}
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{ __('dashboard.code') }}
                                                </th>
                                                <th>
                                                    {{ __('dashboard.name') }}
                                                </th>
                                                <th>
                                                    {{ __('dashboard.date') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentItems ?? [] as $item)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $item->code ?? '-' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $item->name ?? '-' }}
                                                    </td>
                                                    <td style="font-size: 0.875rem">
                                                        {{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5">
                                                        لا توجد بيانات
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- آخر 5 عمليات -->
                @if (tenant()->hasModule('invoices'))
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem">
                                <h5 class="mb-0 fw-bold tables-section-title">
                                    <i data-lucide="file-text"
                                        style="
                                            width: 20px;
                                            height: 20px;
                                            margin-left: 8px;
                                            vertical-align: middle;
                                        "></i>
                                    {{ __('dashboard.recent_operations') }}
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{ __('dashboard.number') }}
                                                </th>
                                                <th>
                                                    {{ __('dashboard.client') }}
                                                </th>
                                                <th>
                                                    {{ __('dashboard.amount') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOperations ?? [] as $operation)
                                                <tr>
                                                    <td>
                                                        <strong>#{{ $operation->pro_id ?? '-' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $operation->acc1Head->aname ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($operation->fat_net ?? 0, 2) }}
                                                        {{ __('dashboard.currency') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5">
                                                        {{ __('dashboard.no_data') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Masar theme switcher --}}
    <script src="{{ asset('js/theme-switcher.js') }}"></script>
    <script>
        (function() {
            if (typeof MasarThemeSwitcher !== "undefined") {
                // Bind to dropdown items instead of select
                MasarThemeSwitcher.bindDropdown(
                    "[data-masar-theme-dropdown]"
                );
            }
        })();
    </script>

    <script>
        // Initialize Lucide icons when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchCount = document.getElementById('searchCount');

            function performSearch() {
                if (!searchInput) {
                    console.error('Search input not found');
                    return;
                }

                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                // Get the apps-icons-row container
                const appsRow = document.querySelector('.apps-icons-row');
                if (!appsRow) {
                    console.error('Apps icons row (.apps-icons-row) not found');
                    return;
                }

                // Get all app icons inside apps-icons-row
                const appIcons = appsRow.querySelectorAll('.app-icon-large');

                console.log('Found', appIcons.length, 'app icons in .apps-icons-row');

                if (appIcons.length === 0) {
                    console.warn('No app icons found in .apps-icons-row');
                    return;
                }

                // Search in app icons
                appIcons.forEach(function(icon) {
                    const appText = icon.querySelector('p');
                    if (appText) {
                        const text = appText.textContent.toLowerCase().trim();
                        const matches = text.includes(searchTerm);

                        if (matches || searchTerm === '') {
                            // Show icon - remove all hiding styles
                            icon.classList.remove('hidden');
                            icon.style.cssText = '';
                            visibleCount++;
                        } else {
                            // Hide icon - use multiple methods to ensure it works
                            icon.classList.add('hidden');
                            icon.style.setProperty('display', 'none', 'important');
                            icon.style.setProperty('visibility', 'hidden', 'important');
                            icon.style.setProperty('opacity', '0', 'important');
                            icon.style.setProperty('height', '0', 'important');
                            icon.style.setProperty('width', '0', 'important');
                            icon.style.setProperty('margin', '0', 'important');
                            icon.style.setProperty('padding', '0', 'important');
                        }
                    } else {
                        // If no p tag, show the icon anyway
                        icon.classList.remove('hidden');
                        icon.style.cssText = '';
                        visibleCount++;
                    }
                });

                console.log('Search term:', searchTerm, 'Visible count:', visibleCount);

                // Update search count
                if (searchCount) {
                    if (searchTerm !== '') {
                        searchCount.textContent = visibleCount + ' {{ __('dashboard.results') }}';
                        searchCount.style.display = 'block';
                    } else {
                        searchCount.style.display = 'none';
                    }
                }
            }

            if (searchInput) {
                // Initial search to show all icons
                performSearch();

                // Add event listeners
                searchInput.addEventListener('input', performSearch);
                searchInput.addEventListener('keyup', performSearch);
                searchInput.addEventListener('paste', function() {
                    setTimeout(performSearch, 10);
                });

                // Also listen for change event
                searchInput.addEventListener('change', performSearch);
            } else {
                console.error('Search input element not found');
            }
        });

        // Reinitialize icons if Lucide loads after DOM
        window.addEventListener('load', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Re-run search after page fully loads
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value) {
                const event = new Event('input');
                searchInput.dispatchEvent(event);
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- SweetAlert2 for logout confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts
</body>

</html>
