<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Massar | Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard-main.css') }}">

    <!-- Lucide Icons CDN -->
    <script src="{{ asset('assets/js/lucide.js') }}"></script>
</head>

<body class="theme-neumorphism-lite">

    <!-- Animated Doodles Background - Geometric Shapes, Currency & ERP Icons -->
    <div class="doodles-container">
        <!-- Dollar Sign Icon -->
        <svg class="doodle doodle-1" width="80" height="80" viewBox="0 0 80 80"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="30" stroke="#239d77" stroke-width="2.5" fill="none"
                opacity="0.25" />
            <text x="40" y="50" font-family="Arial, sans-serif" font-size="40" font-weight="bold" fill="#239d77"
                opacity="0.3" text-anchor="middle">$</text>
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
                opacity="0.3" text-anchor="middle">€</text>
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
                opacity="0.4" text-anchor="middle">$</text>
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

    <!-- Header Section - أول شيء في الصفحة -->
    <div class="header-section">
        <div class="header-container">
            <div class="header-top-row">
                <h1 class="title text-white text-page-title">Massar ERP</h1>
                <div class="user-section">
                    <i data-lucide="user" class="user-icon"></i>
                    <span class="user-name">{{ auth()->user()->name ?? 'المستخدم' }}</span>
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn" title="تسجيل الخروج">
                            <i data-lucide="log-out" class="logout-icon"></i>
                            <span class="logout-text">تسجيل الخروج</span>
                        </button>
                    </form>
                </div>
                <div class="search-container-inline">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" id="searchInput" class="search-input frst" placeholder="🔍 ابحث عن القسم...">
                    <span class="search-count" id="searchCount"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-container">

        <!-- كروت الإحصائيات -->
        <div class="stats-cards-section">
            <div class="row g-3 stats-cards-row">
                <!-- كرت العملاء -->
                <div class="col-lg-4 col-md-4 stats-card-col">
                    <div class="card border-0 shadow-lg h-100 stats-card stats-card-clients">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="stats-card-content">
                                    <p class="stats-card-label">العملاء</p>
                                    <h2 class="stats-card-value">{{ number_format($totalClients ?? 0) }}</h2>
                                    <p class="stats-card-subtitle">
                                        <i data-lucide="trending-up"></i>
                                        إجمالي العملاء
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
                                    <p class="stats-card-label">مرات الدخول</p>
                                    <h2 class="stats-card-value">{{ number_format($totalLogins ?? 0) }}</h2>
                                    <p class="stats-card-subtitle">
                                        <i data-lucide="activity"></i>
                                        إجمالي الجلسات
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
                                    <p class="stats-card-label">المبيعات اليوم</p>
                                    <h2 class="stats-card-value">{{ number_format($todaySales ?? 0, 2) }}</h2>
                                    <p class="stats-card-subtitle">
                                        <i data-lucide="dollar-sign"></i>
                                        ر.س
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

        @php
            $subscriptionEnd = tenant()->getSubscriptionEndDate();
            $daysRemaining = null;
            if ($subscriptionEnd) {
                // حساب الفرق بالأيام الكاملة (بداية اليوم الحالي مقابل بداية يوم الانتهاء)
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
                        <strong class="d-block mb-1">تنبيه: اشتراكك على وشك الانتهاء</strong>
                        <span>
                            سينتهي اشتراكك بعد {{ $daysRemaining }} يوم، بتاريخ
                            {{ \Carbon\Carbon::parse($subscriptionEnd)->format('Y-m-d') }}
                        </span>

                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="{{ app()->getLocale() === 'ar' ? 'left: 1rem; right: auto;' : '' }}"></button>
            </div>
        @endif

        <!-- مجموعة الإعدادات الأساسية -->
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
            'view items',
            'view units',
            'view prices',
            'view notes-names',
            'view varibals',
            'view
            varibalsValues',
            'view roles',
            'view branches',
            'view settings',
            'view login-history',
            'view active-sessions',
            'view activity-logs',
            'view Inquiries',
            'view Orders',
            'view Rental-Management',
            'view progress-recyclebin',
            'view progress-project-types',
            'view progress-project-templates',
            'view progress-item-statuses',
            'view progress-work-items',
            'view progress-work-item-categories',
            'view daily-progress',
            'view progress-issues',
            'view progress-projects',
            'view progress-dashboard',
            ])
            <div class="apps-icons-row">
                <div class="d-flex">
                    {{-- البيانات الاساسيه --}}
                    @canany([
                        'view Clients',
                        'view Suppliers',
                        'view Funds',
                        'view Banks',
                        'view Employees',
                        'view warhouses',
                        'view Expenses',
                        'view Revenues',
                        'view various_creditors',
                        'view various_debtors',
                        'view partners',
                        'view current_partners',
                        'view assets',
                        'view rentables',
                        'view check-portfolios-incoming',
                        'view basicData-statistics',
                        ])
                        <a href="{{ route('accounts.index') }}" class="app-icon-large icon-bg-green">
                            <div class="icon-wrapper">
                                <i data-lucide="chart-bar-increasing"></i>
                            </div>
                            <p>البيانات الاساسيه</p>
                        </a>
                    @endcanany

            <div class="apps-grid">
                <!-- الإعدادات الأساسية -->
                @canany([
                    'view Clients',
                    'view Suppliers',
                    'view Funds',
                    'view Banks',
                    'view Employees',
                    'view warhouses',
                    'view Expenses',
                    'view Revenues',
                    'view various_creditors',
                    'view various_debtors',
                    'view partners',
                    'view current_partners',
                    'view assets',
                    'view rentables',
                    'view check-portfolios-incoming',
                    'view basicData-statistics',
                    'view items',
                    'view units',
                    'view prices',
                    'view notes-names',
                    'view varibals',
                    'view varibalsValues',
                    'view roles',
                    'view branches',
                    'view settings',
                    'view login-history',
                    'view active-sessions',
                    'view activity-logs',
                    ])
                    <div class="group-apps-grid">
                        {{-- البيانات الاساسيه --}}
                        @if (tenant()->hasModule('accounts'))
                            @canany([
                                'view Clients',
                                'view Suppliers',
                                'view Funds',
                                'view Banks',
                                'view Employees',
                                'view
                                warhouses',
                                'view Expenses',
                                'view Revenues',
                                'view various_creditors',
                                'view various_debtors',
                                'view
                                partners',
                                'view current_partners',
                                'view assets',
                                'view rentables',
                                'view check-portfolios-incoming',
                                'view basicData-statistics',
                                ])
                                <a href="{{ route('accounts.index') }}" class="app-card">
                                    <div class="app-icon" style="background-color: white;">
                                        <i data-lucide="chart-bar-increasing"
                                            style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                    </div>
                                    <p class="app-name">البيانات الاساسيه</p>
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
                                <a href="{{ route('items.index') }}" class="app-card">
                                    <div class="app-icon" style="background-color: white;">
                                        <i data-lucide="boxes"
                                            style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                    </div>
                                    <p class="app-name">الاصناف</p>
                                </a>
                            @endcanany
                        @endif


                        {{-- الصلاحيات --}}
                        @canany(['view roles', 'view branches', 'view settings', 'view login-history', 'view active-sessions',
                            'view activity-logs'])
                            <a href="{{ route('users.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="key" style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">الصلاحيات</p>
                            </a>
                        @endcanany

                        {{-- الاعدادات --}}
                        @can('view settings')
                            <a href="{{ route('mysettings.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="settings"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">الاعدادات</p>
                            </a>
                        @endcan

                        {{-- التقارير --}}
                        @canany(['view DailyWorkAnalysis', 'view Chart-of-Accounts', 'view balance-sheet', 'view Profit-Loss',
                            'view Sales-Reports', 'view Purchasing-Reports', 'view Inventory-Reports', 'view Expenses-Reports'])
                            <a href="{{ route('reports.overall') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="file-bar-chart"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">التقارير</p>
                            </a>
                        @endcanany
                    </div>
                @endcanany

                <div class="group-apps-grid">
                    {{-- crm --}}
                    @if (tenant()->hasModule('crm'))
                        @canany(['view CRM', 'view CRM Statistics'])
                            <a href="{{ route('statistics.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="user-cog"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">CRM</p>
                            </a>
                        @endcanany
                    @endif

                    {{-- المبيعات --}}
                    @if (tenant()->hasModule('invoices'))
                        @can('view Sales Invoice')
                            <a href="{{ route('invoices.index', ['type' => 10]) }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="trending-up"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">المبيعات</p>
                            </a>
                        @endcan
                    @endif

                    {{-- pos --}}
                    @if (tenant()->hasModule('pos'))
                        <!-- @can('view POS')
        -->
                            <a href="{{ route('pos.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="shopping-cart"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">نقطة البيع</p>
                            </a>
                            <!--
    @endcan -->
                    @endif


                    {{-- ادارة المستأجرات --}}
                    @if (tenant()->hasModule('rentals'))
                        @can('view Buildings')
                            <a href="{{ route('rentals.buildings.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="building"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">ادارة المستأجرات</p>
                            </a>
                        @endcan
                    @endif
                </div>

                    {{-- ادارة الحسابات --}}
                    @if (tenant()->hasModule('accounts'))
                        @can('view journals')
                            <a href="{{ route('journals.index', ['type' => 'basic_journal']) }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="file-text"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">أدارة الحسابات</p>
                            </a>
                        @endcan

                        {{-- ادارة المصروفات --}}
                        {{-- @can('view Expenses-Management') --}}
                        <a href="{{ route('expenses.dashboard') }}" class="app-card">
                            <div class="app-icon" style="background-color: white;">
                                <i data-lucide="credit-card"
                                    style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                            </div>
                            <p>ادارة المصروفات</p>
                        </a>
                        {{-- @endcan --}}

                        {{--   السندات الماليه --}}
                        @canany(['view receipt vouchers', 'view payment vouchers', 'view exp-payment'])
                            <a href="{{ route('vouchers.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="receipt"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">السندات الماليه</p>
                            </a>
                        @endcanany
                        {{-- التحويلات  النقديه --}}
                        @can('view transfers')
                            <a href="{{ route('transfers.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="arrow-left-right"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">التحويلات النقديه</p>
                            </a>
                        @endcan
                    @endif

                    {{-- ادارة الدفعات -?user --}}
                    @if (tenant()->hasModule('installments'))
                        @can('view Installment Plans')
                            <a href="{{ route('installments.plans.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="tag"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">ادارة الدفعات</p>
                            </a>
                        @endcan
                    @endif

                    {{-- ادارة الشيكات --}}
                    @if (tenant()->hasModule('checks'))
                        @can('view Checks')
                            <a href="{{ route('checks.incoming') }}" class="app-card">
                                <span class="new-badge">جديد 🎉</span>
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="file-check-2"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">إدارة الشيكات</p>
                            </a>
                        @endcan
                    @endif
                </div>

                    {{-- ادارة المخزون --}}
                    @if (tenant()->hasModule('invoices'))
                        @canany([
                            'view Inventory-Management',
                            'view Damaged Goods Invoice',
                            'view Dispatch Order',
                            'view
                            Addition Order',
                            'view Store-to-Store Transfer',
                            ])
                            <a href="{{ route('invoices.index', ['type' => 18]) }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="package"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">ادارة المخزون</p>
                            </a>
                        @endcanany
                    @endif


                    {{-- التصنيع --}}
                    @if (tenant()->hasModule('manufacturing'))
                        @can('view Manufacturing Invoices')
                            <a href="{{ route('manufacturing.create') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="factory"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">التصنيع</p>
                            </a>
                        @endcan
                    @endif
                    {{-- إدارة الجودة --}}
                    {{-- Note: quality is not in modules_list, assuming it belongs to manufacturing or standalone? --}}
                    {{-- For now, let's keep it under manufacturing if it's not specified --}}
                    @if (tenant()->hasModule('quality'))
                        @canany([
                            'view quality',
                            'view inspections',
                            'view standards',
                            'view ncr',
                            'view capa',
                            'view
                            batches',
                            'view rateSuppliers',
                            'view certificates',
                            'view audits',
                            ])
                            <a href="{{ route('quality.dashboard') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="award"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">إدارة الجودة</p>
                            </a>
                        @endcanany
                    @endif

                    {{-- المشتريات --}}
                    @if (tenant()->hasModule('invoices'))
                        @can('view Purchase Invoice')
                            <a href="{{ route('invoices.index', ['type' => 11]) }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="shopping-bag"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">المشتريات</p>
                            </a>
                        @endcan
                    @endif

                    {{-- الصيانه --}}
                    @if (tenant()->hasModule('maintenance'))
                        @canany([
                            'view Service Types',
                            'view Maintenances',
                            'view Periodic Maintenance',
                            'view
                            Maintenance',
                            ])
                            <a href="{{ route('service.types.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="package"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">الصيانه</p>
                            </a>
                        @endcanany
                    @endif

                    {{-- إدارة الأسطول --}}
                    @if (tenant()->hasModule('fleet'))
                        @can('view Fleet Dashboard')
                            <a href="{{ route('fleet.dashboard.index') }}?sidebar=fleet" class="app-card">
                                <span class="new-badge">جديد 🎉</span>
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="truck"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">إدارة الأسطول</p>
                            </a>
                        @endcan
                    @endif
                </div>

                <!-- المشاريع والإنتاج -->
                <div class="group-apps-grid">
                    {{-- المشاريع  --}}
                    @if (tenant()->hasModule('projects'))
                        @can('view Projects')
                            <a href="{{ route('progress.project.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="kanban"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">المشاريع</p>
                            </a>
                        @endcan
                    @endif

                    {{-- التقدم اليومي --}}
                    @if (tenant()->hasModule('daily_progress'))
                        <a href="{{ route('progress.project.index') }}" class="app-card">
                            <div class="app-icon" style="background-color: white;">
                                <i data-lucide="bar-chart-3"
                                    style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                            </div>
                            <p class="app-name">التقدم اليومي</p>
                        </a>
                    @endif

                    {{-- عمليات الاصول  --}}
                    @if (tenant()->hasModule('depreciation'))
                        {{-- @can('view Asset-Operations') --}}
                        <a href="{{ route('depreciation.index') }}" class="app-card">
                            <div class="app-icon" style="background-color: white;">
                                <i data-lucide="building"
                                    style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                            </div>
                            <p class="app-name">عمليات الاصول</p>
                        </a>
                        {{-- @endcan --}}
                    @endif

                    {{-- ادارة الموارد  --}}
                    @if (tenant()->hasModule('myResources'))
                        @can('view MyResources')
                            <a href="{{ route('myresources.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="cog"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">إدارة الموارد</p>
                            </a>
                        @endcan
                    @endif
                </div>

                    {{-- ادارة الموارد --}}
                    @can('view MyResources')
                        <a href="{{ route('myresources.index') }}" class="app-icon-large icon-bg-green">
                            <div class="icon-wrapper">
                                <i data-lucide="cog"></i>
                            </div>
                            <p>إدارة الموارد</p>
                        </a>
                    @endcan

                    {{-- الموارد البشريه --}}
                    @if (tenant()->hasModule('hr'))
                        @can('view Employees')
                            <a href="{{ route('employees.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="users"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">الموارد البشريه</p>
                            </a>
                        @endcan
                        {{-- بصمة الموبايل  --}}
                        @can('view Mobile-fingerprint')
                            <a href="{{ route('mobile.employee-login') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="fingerprint"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">بصمه الموبايل</p>
                            </a>
                        @endcan
                    @endif
                </div>

                <!-- الخدمات والعمليات -->
                <div class="group-apps-grid">
                    {{-- ادارة المستأجرات  --}}
                    @if (tenant()->hasModule('rentals'))
                        @can('view Rental-Management')
                            <a href="{{ route('rentals.buildings.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="building"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">ادارة المستأجرات</p>
                            </a>
                        @endcan
                    @endif


                    {{-- أدارة الشحن --}}
                    @if (tenant()->hasModule('shipping'))
                        @can('view Orders')
                            <a href="{{ route('orders.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="truck"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">أدارة الشحن</p>
                            </a>
                        @endcan
                    @endif

                    {{-- Inquiries --}}
                    @if (tenant()->hasModule('inquiries'))
                        @can('view Inquiries')
                            <a href="{{ route('inquiries.index') }}" class="app-card">
                                <div class="app-icon" style="background-color: white;">
                                    <i data-lucide="layers"
                                        style="color: #00695C; width: 24px; height: 24px; stroke-width: 2;"></i>
                                </div>
                                <p class="app-name">Inquiries</p>
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
        @endcanany

        <!-- الجداول (3 في الصف) -->
        <div class="tables-section" style="margin-top: 3rem;">
            <div class="row g-4">
                <!-- آخر 5 حسابات -->
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem;">
                            <h5 class="mb-0 fw-bold" style="color: #2d3748 !important;">
                                <i data-lucide="wallet" style="width: 20px; height: 20px; margin-left: 8px; vertical-align: middle;"></i>
                                آخر 5 حسابات
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>الكود</th>
                                            <th>الاسم</th>
                                            <th>الرقم</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentAccounts ?? [] as $account)
                                            <tr>
                                                <td><strong>{{ $account->code ?? '-' }}</strong></td>
                                                <td>{{ $account->aname ?? '-' }}</td>
                                                <td style="color: #2d3748 !important;">#{{ $account->id }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5" style="font-size: 0.95rem; color: #9ca3af !important;">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- آخر 5 عمليات تسجيل دخول -->
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem;">
                            <h5 class="mb-0 fw-bold" style="color: #2d3748 !important;">
                                <i data-lucide="log-in" style="width: 20px; height: 20px; margin-left: 8px; vertical-align: middle;"></i>
                                آخر 5 عمليات تسجيل دخول
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>IP</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLogins ?? [] as $login)
                                            <tr>
                                                <td><strong>{{ $login->user->name ?? '-' }}</strong></td>
                                                <td style="color: #2d3748 !important; font-size: 0.875rem;">{{ $login->ip_address ?? '-' }}</td>
                                                <td style="color: #2d3748 !important; font-size: 0.875rem;">
                                                    {{ $login->login_at ? $login->login_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5" style="font-size: 0.95rem; color: #9ca3af !important;">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات المبيعات -->
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem;">
                            <h5 class="mb-0 fw-bold" style="color: #2d3748 !important;">
                                <i data-lucide="trending-up" style="width: 20px; height: 20px; margin-left: 8px; vertical-align: middle;"></i>
                                إحصائيات المبيعات
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                    <span class="sales-stats-label">آخر فاتورة</span>
                                    <span class="sales-stats-value">
                                        {{ $salesStats['last_invoice'] ? '#' . $salesStats['last_invoice']->pro_id . ' - ' . number_format($salesStats['last_invoice']->fat_net ?? 0, 2) . ' ر.س' : '-' }}
                                    </span>
                                </div>
                                <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                    <span class="sales-stats-label">أحر يوم</span>
                                    <span class="sales-stats-value">
                                        {{ number_format($salesStats['today'] ?? 0, 2) }} ر.س
                                    </span>
                                </div>
                                <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                    <span class="sales-stats-label">آخر أسبوع</span>
                                    <span class="sales-stats-value">
                                        {{ number_format($salesStats['last_week'] ?? 0, 2) }} ر.س
                                    </span>
                                </div>
                                <div class="sales-stats-item d-flex justify-content-between align-items-center">
                                    <span class="sales-stats-label">آخر شهر</span>
                                    <span class="sales-stats-value">
                                        {{ number_format($salesStats['last_month'] ?? 0, 2) }} ر.س
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- آخر 5 أصناف -->
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem;">
                            <h5 class="mb-0 fw-bold" style="color: #2d3748 !important;">
                                <i data-lucide="package" style="width: 20px; height: 20px; margin-left: 8px; vertical-align: middle;"></i>
                                آخر 5 أصناف
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>الكود</th>
                                            <th>الاسم</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentItems ?? [] as $item)
                                            <tr>
                                                <td><strong>{{ $item->code ?? '-' }}</strong></td>
                                                <td>{{ $item->name ?? '-' }}</td>
                                                <td style="color: #2d3748 !important; font-size: 0.875rem;">
                                                    {{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5" style="font-size: 0.95rem; color: #9ca3af !important;">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- آخر 5 عمليات -->
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom" style="padding: 1rem 1.25rem;">
                            <h5 class="mb-0 fw-bold" style="color: #2d3748 !important;">
                                <i data-lucide="file-text" style="width: 20px; height: 20px; margin-left: 8px; vertical-align: middle;"></i>
                                آخر 5 عمليات
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>الرقم</th>
                                            <th>العميل</th>
                                            <th>المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOperations ?? [] as $operation)
                                            <tr>
                                                <td><strong>#{{ $operation->pro_id ?? '-' }}</strong></td>
                                                <td>{{ $operation->acc1Head->aname ?? '-' }}</td>
                                                <td style="font-weight: 600; color: #2d3748 !important;">
                                                    {{ number_format($operation->fat_net ?? 0, 2) }} ر.س
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-5" style="font-size: 0.95rem; color: #9ca3af !important;">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        searchCount.textContent = visibleCount + ' نتيجة';
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
</body>

</html>
