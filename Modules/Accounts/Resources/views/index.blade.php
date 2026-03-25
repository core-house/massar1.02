@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.accounts')
@endsection

@section('content')
    @php
        $permissionTypes = [
            'clients' => 'Clients',
            'suppliers' => 'Suppliers',
            'funds' => 'Funds',
            'banks' => 'Banks',
            'employees' => 'Employees',
            'warhouses' => 'warhouses',
            'expenses' => 'Expenses',
            'revenues' => 'Revenues',
            'creditors' => 'various_creditors',
            'debtors' => 'various_debtors',
            'partners' => 'partners',
            'current-partners' => 'current_partners',
            'assets' => 'assets',
            'rentables' => 'rentables',
            'check-portfolios-incoming' => 'check-portfolios-incoming',
            'check-portfolios-outgoing' => 'check-portfolios-outgoing',
        ];

        $parentCodes = [
            'clients' => '1103', // العملاء
            'suppliers' => '2101', // الموردين
            'banks' => '1102', // البنوك
            'funds' => '1101', // الصناديق
            'warhouses' => '1104', // المخازن
            'expenses' => '5', // المصروفات
            'revenues' => '42', // الايرادات
            'creditors' => '2104', // دائنين اخرين
            'debtors' => '1106', // مدينين آخرين
            'partners' => '31', // الشريك الرئيسي
            'current-partners' => '3201', // جاري الشريك
            'assets' => '12', // الأصول
            'employees' => '2102', // الموظفين
            'rentables' => '1202', // مباني (أصل قابل للإيجار)
            'check-portfolios-incoming' => '1105', // حافظات أوراق القبض
            'check-portfolios-outgoing' => '2103', // حافظات أوراق الدفع
        ];

        $type = request('type');

        // Arabic labels for display without modifying the original $permissionTypes
        $permissionLabels = [
            'clients' => __('accounts::accounts.types.clients'),
            'suppliers' => __('accounts::accounts.types.suppliers'),
            'funds' => __('accounts::accounts.types.funds'),
            'banks' => __('accounts::accounts.types.banks'),
            'employees' => __('accounts::accounts.types.employees'),
            'warhouses' => __('accounts::accounts.types.warehouses'),
            'expenses' => __('accounts::accounts.types.expenses'),
            'revenues' => __('accounts::accounts.types.revenues'),
            'creditors' => __('accounts::accounts.types.creditors'),
            'debtors' => __('accounts::accounts.types.debtors'),
            'partners' => __('accounts::accounts.types.partners'),
            'current-partners' => __('accounts::accounts.types.current_partners'),
            'assets' => __('accounts::accounts.types.assets'),
            'rentables' => __('accounts::accounts.types.rentables'),
            'check-portfolios-incoming' => __('accounts::accounts.types.check_portfolios_incoming'),
            'check-portfolios-outgoing' => __('accounts::accounts.types.check_portfolios_outgoing'),
        ];

        // Prefer the Arabic label when available, otherwise fall back to the original mapping
        $permName = $permissionLabels[$type] ?? ($permissionTypes[$type] ?? null);
        $parentCode = $parentCodes[$type] ?? null;
    @endphp

    <div class="container-dashboard">
        <!-- Page Header -->
    

        {{-- رسائل الأخطاء --}}
        @if (session('error'))
            <div class="alert alert-danger mt-3 border-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- البريدكرامب --}}
        <section class="content-header mb-4">
            <div class="container-fluid">
                @include('components.breadcrumb', [
                    'title' => $permName ? __('accounts::accounts.accounts_list') . ' - ' . $permName : __('accounts::accounts.accounts_list'),
                    'breadcrumb_items' => [
                        ['label' => __('accounts::accounts.home'), 'url' => route('admin.dashboard')],
                        $permName ? ['label' => $permName] : ['label' => __('accounts::accounts.accounts_list')],
                    ],
                ])
            </div>
        </section>

        {{-- الأكشنات (إضافة + بحث) --}}
        <div class="row">
            <div class="flex-shrink-0 col-md-8">
                @if ($type == 'current-partners')
                    <p class="p-3 rounded-lg" style="background: linear-gradient(135deg, #34d3a3 0%, #239d77 100%); color: white;">
                        {{ __('accounts::accounts.account_added_with_partner') }}
                    </p>
                @elseif(
                    $type &&
                        isset($permissionTypes[$type]) &&
                        auth()->user()->can('create ' . $permissionTypes[$type]))
                    <a href="{{ route('accounts.create', ['parent' => $parentCode]) }}" class="btn btn-main">
                        <i class="las la-plus"></i> {{ __('accounts::accounts.add_new_account') }}
                    </a>
                @elseif(!$type)
                    <a href="{{ route('accounts.create', ['parent' => $parentCode]) }}" class="btn btn-main">
                        <i class="las la-plus"></i> {{ __('accounts::accounts.add_new_account') }}
                    </a>
                @endif
            </div>

            <div class="col-md-4">
                <form method="GET" action="{{ route('accounts.index') }}" class="flex gap-2">
                    <div class="row">
                        <div class="col-8">
                    @if ($type)
                        <input type="hidden" name="type" value="{{ $type }}">
                    @endif
                    
                    <input class="input form-control" type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('accounts::accounts.search_by_account') }}" autocomplete="off">
                        </div>
                        <div class="col-3">
                    <button type="submit" class="btn btn-lg btn-main col-2">
                        <i class="las la-search text-lg"></i>
                    </button>
                    </div>
                    <div class="col-2"> 
                       
                    @if (request('search'))
                        <a href="{{ route('accounts.index', ['type' => $type]) }}" class="btn btn-outline col-2">
                            <i class="las la-times"></i>
                        </a>
                    @endif
                    </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- الجدول --}}
        <div class="card hover-lift transition-base" style="border-left: 4px solid #34d3a3; ">
            <div class="card-header border-b border-border-light p-4 d-flex justify-content-between align-items-center">
                <h3 class="text-section-title mb-0">{{ $permName ? __('accounts::accounts.list') . ' ' . $permName : __('accounts::accounts.accounts_list') }}</h3>
                @php
                    $printPermission = $type && isset($permissionTypes[$type]) 
                        ? 'print ' . $permissionTypes[$type] 
                        : null;
                @endphp
                <x-table-export-actions 
                    table-id="myTable" 
                    filename="accounts" 
                    :excel-label="__('accounts::accounts.export_excel')"
                    :pdf-label="__('accounts::accounts.export_pdf')" 
                    :print-label="__('accounts::accounts.print')"
                    :print-permission="$printPermission" />
            </div>

            <div class="table-responsive px-4 py-2" id="printed" style="overflow-x: auto;">
                <table id="myTable" class="table table-sticky">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('accounts::accounts.name') }}</th>
                            <th>{{ __('accounts::accounts.balance') }}</th>
                            <th>{{ __('accounts::accounts.address') }}</th>
                            <th>{{ __('accounts::accounts.phone') }}</th>
                            <th>ID</th>
                            <th>{{ __('accounts::accounts.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $index => $acc)
                            <tr>
                                <td class="text-table">{{ $accounts->firstItem() + $index }}</td>
                                <td class="text-table">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-text-primary">
                                            <i class="text-text-tertiary small">{{ $acc->code }}</i> - {{ Str::limit($acc->aname, 40) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-table">
                                    @if (!$acc->secret)
                                        <a class="btn btn-outline btn-sm"
                                            href="{{ route('account-movement', ['accountId' => $acc->id]) }}">
                                            {{ number_format($acc->balance ?? 0, 2) }}
                                        </a>
                                    @else
                                        <span class="text-text-tertiary">----</span>
                                    @endif
                                </td>
                                <td class="text-table">
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;"
                                        title="{{ $acc->address }}">
                                        {{ $acc->address ?? '__' }}
                                    </span>
                                </td>
                                <td class="text-table">{{ $acc->phone ?? '__' }}</td>
                                <td class="text-table">
                                    <span class="badge badge-neutral">{{ $acc->id }}</span>
                                </td>
                                <td class="text-table">
                                    @php
                                        // تحديد نوع الحساب من الكود - ترتيب من الأطول للأقصر
                                        $accountType = null;
                                        $sortedCodes = $parentCodes;
                                        uksort($sortedCodes, function ($a, $b) use ($parentCodes) {
                                            return strlen($parentCodes[$b]) - strlen($parentCodes[$a]);
                                        });

                                        foreach ($sortedCodes as $typeKey => $code) {
                                            if (str_starts_with($acc->code, $code)) {
                                                $accountType = $typeKey;
                                                break;
                                            }
                                        }
                                        $permissionName =
                                            $accountType && isset($permissionTypes[$accountType])
                                                ? $permissionTypes[$accountType]
                                                : 'accounts';
                                    @endphp
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if (auth()->user()->can('edit ' . $permissionName))
                                            <a href="{{ route('accounts.edit-direct', $acc->id) }}" 
                                                class="btn btn-success btn-sm" 
                                                title="{{ __('accounts::accounts.edit') }}">
                                                <i class="las la-pen"></i>
                                            </a>
                                        @endif

                                        @if (
                                            $acc->deletable &&
                                                auth()->user()->can('delete ' . $permissionName))
                                            <form action="{{ route('accounts.destroy', $acc->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" 
                                                    title="{{ __('accounts::accounts.delete') }}"
                                                    onclick="return confirm('{{ __('accounts::accounts.confirm_delete') }}')">
                                                    <i class="las la-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">
                                    <div class="alert alert-info py-3 mb-0" style="background: #e6f2ff; border-left: 4px solid #1a8eff; color: #0075e6;">
                                        <i class="las la-info-circle me-2"></i>
                                        @if (!$type)
                                            {{ __('accounts::accounts.please_select_account_type') }}
                                        @elseif (request('search'))
                                            {{ __('accounts::accounts.no_results_for') }} "{{ request('search') }}"
                                        @else
                                            {{ __('accounts::accounts.no_data') }}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($accounts->hasPages())
                <div class="card-footer border-t border-border-light p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="text-body-sm text-text-secondary">
                            {{ __('accounts::accounts.showing') }} {{ $accounts->firstItem() }} {{ __('accounts::accounts.to') }} {{ $accounts->lastItem() }}
                            {{ __('accounts::accounts.of') }} {{ $accounts->total() }} {{ __('accounts::accounts.account') }}
                        </div>

                        <div>
                            {{ $accounts->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection


