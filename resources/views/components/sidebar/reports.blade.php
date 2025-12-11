{{-- التقارير العامة --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-chart-pie"></i>
        التقارير العامة
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.overall') }}">
                    <i class="ti-control-record"></i>محلل العمل اليومي
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.daily-activity-analyzer') }}">
                    <i class="ti-control-record"></i>محلل النشاط اليومي
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.journal-summery') }}">
                    <i class="ti-control-record"></i>اليومية العامة
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-journal-details') }}">
                    <i class="ti-control-record"></i>كشف حساب عام
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-account-statement-report') }}">
                    <i class="ti-control-record"></i>كشف حساب عام
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.oper-aging') }}">
                    <i class="ti-control-record"></i>تقرير الأعمار
                </a>
            </li> --}}
        </ul>
</li>

{{-- تقارير الحسابات --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-book"></i>
        تقارير الحسابات
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.accounts-tree') }}">
                    <i class="ti-control-record"></i>شجرة الحسابات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-balance-sheet') }}">
                    <i class="ti-control-record"></i>الميزانية العمومية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-profit-loss-report') }}">
                    <i class="ti-control-record"></i>أرباح وخسائر
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-profit-loss-report-total') }}">
                    <i class="ti-control-record"></i>قائمة الدخل لإجمالي الفترة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-account-balances') }}">
                    <i class="ti-control-record"></i>ميزان الحسابات
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-account-statement') }}">
                    <i class="ti-control-record"></i>كشف حساب
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-account-balances-by-store') }}">
                    <i class="ti-control-record"></i>ميزان الحسابات حسب المستودع
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('account-movement') }}">
                    <i class="ti-control-record"></i>حركة حساب
                </a>
            </li>
        </ul>
</li>

{{-- تقارير المخزون والأصناف --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-boxes"></i>
        تقارير المخزون
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-inventory-balances') }}">
                    <i class="ti-control-record"></i>قائمة الأصناف مع الأرصدة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-inventory-balances-by-store') }}">
                    <i class="ti-control-record"></i>الأرصدة حسب المستودع
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-inventory-movements') }}">
                    <i class="ti-control-record"></i>حركة الصنف
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-inventory-report') }}">
                    <i class="ti-control-record"></i>تقرير المخزون العام
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-inventory-daily-movement-report') }}">
                    <i class="ti-control-record"></i>حركة المخزون اليومية
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-inventory-stocktaking-report') }}">
                    <i class="ti-control-record"></i>جرد المخزون
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.get-items-max-min-quantity') }}">
                    <i class="ti-control-record"></i>حد الطلب الأدنى والأقصى
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('prices.compare.report') }}">
                    <i class="ti-control-record"></i>مقارنة الأسعار
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.items.inactive') }}">
                    <i class="ti-control-record"></i>الأصناف الموقوفة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.items.with-stores') }}">
                    <i class="ti-control-record"></i>الأصناف لكل مستودع
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.inventory-discrepancy-report') }}">
                    <i class="ti-control-record"></i>مراقبة جرد الأصناف
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.items.check-all-quantity-limits') }}">
                    <i class="ti-control-record"></i>فحص حدود الكميات لجميع الأصناف
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.items.with-quantity-issues') }}">
                    <i class="ti-control-record"></i>الأصناف بمشاكل الكميات
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.items.clear-all-notifications') }}">
                    <i class="ti-control-record"></i>مسح جميع الإشعارات
                </a>
            </li> --}}
        </ul>
</li>

{{-- تقارير المبيعات --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-shopping-cart"></i>
        تقارير المبيعات
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-sales-report') }}">
                    <i class="ti-control-record"></i>تقرير المبيعات العام
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.sales.daily') }}">
                    <i class="ti-control-record"></i>المبيعات اليومية
                </a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-sales-daily-report') }}">
                    <i class="ti-control-record"></i>تقرير المبيعات اليومية العام
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.sales.total') }}">
                    <i class="ti-control-record"></i>المبيعات إجماليات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.sales.items') }}">
                    <i class="ti-control-record"></i>المبيعات أصناف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.sales.representative') }}">
                    <i class="ti-control-record"></i>المبيعات حسب المندوب
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-sales-report-by-address') }}">
                    <i class="ti-control-record"></i>المبيعات بالعنوان
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.item-sales') }}">
                    <i class="ti-control-record"></i>مبيعات صنف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('sales.invoice-report') }}">
                    <i class="ti-control-record"></i>فواتير المبيعات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('sales-orders-report') }}">
                    <i class="ti-control-record"></i>أوامر البيع
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('purchase-quotations-reports') }}">
                    <i class="ti-control-record"></i>عروض الأسعار للعملاء
                </a>
            </li>
        </ul>
</li>

{{-- تقارير المشتريات --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-shopping-basket"></i>
        تقارير المشتريات
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-purchases-report') }}">
                    <i class="ti-control-record"></i>تقرير المشتريات العام
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-purchases-daily-report') }}">
                    <i class="ti-control-record"></i>المشتريات اليومية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.purchases.total') }}">
                    <i class="ti-control-record"></i>المشتريات إجماليات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.purchases.items') }}">
                    <i class="ti-control-record"></i>المشتريات أصناف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.item-purchase') }}">
                    <i class="ti-control-record"></i>مشتريات صنف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('billing.invoice-report') }}">
                    <i class="ti-control-record"></i>فواتير المشتريات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('supplier-rfqs-report') }}">
                    <i class="ti-control-record"></i>عروض الأسعار من الموردين
                </a>
            </li>
        </ul>
</li>

{{-- تقارير العملاء --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-users"></i>
        تقارير العملاء
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-customers-report') }}">
                    <i class="ti-control-record"></i>تقرير العملاء العام
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-customers-daily-report') }}">
                    <i class="ti-control-record"></i>تقرير العملاء اليومية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-customers-items-report') }}">
                    <i class="ti-control-record"></i>تقرير العملاء أصناف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-customers-total-report') }}">
                    <i class="ti-control-record"></i>تقرير العملاء إجماليات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.customer-debt-history') }}">
                    <i class="ti-control-record"></i>أعمار ديون العملاء
                </a>
            </li>
        </ul>
</li>

{{-- تقارير الموردين --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-truck"></i>
        تقارير الموردين
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-suppliers-report') }}">
                    <i class="ti-control-record"></i>تقرير الموردين العام
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-suppliers-daily-report') }}">
                    <i class="ti-control-record"></i>تقرير الموردين اليومية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-suppliers-items-report') }}">
                    <i class="ti-control-record"></i>تقرير الموردين أصناف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-suppliers-total-report') }}">
                    <i class="ti-control-record"></i>تقرير الموردين إجماليات
                </a>
            </li>
        </ul>
</li>

{{-- تقارير المصروفات --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-file-invoice-dollar"></i>
        تقارير المصروفات
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-expenses-report') }}">
                    <i class="ti-control-record"></i>تقرير المصروفات العام
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-expenses-daily-report') }}">
                    <i class="ti-control-record"></i>كشف حساب مصروف
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.expenses-balance-report') }}">
                    <i class="ti-control-record"></i>ميزان المصروفات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-cost-centers-report') }}">
                    <i class="ti-control-record"></i>تقرير مراكز التكلفة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-cost-center-account-statement') }}">
                    <i class="ti-control-record"></i>كشف حساب مركز التكلفة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-cost-centers-list') }}">
                    <i class="ti-control-record"></i>قائمة مراكز التكلفة
                </a>
            </li>
        </ul>
</li>

{{-- تقارير النقدية والبنوك --}}
<li class="nav-item has-submenu">
    <a class="nav-link" href="javascript: void(0);">
        <i class="fas fa-money-bill-wave"></i>
        النقدية والبنوك
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-cashbox-movement-report') }}">
                    <i class="ti-control-record"></i>حركة الصندوق
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('reports.general-cash-bank-report') }}">
                    <i class="ti-control-record"></i>حركة البنك
                </a>
            </li>
        </ul>
</li>

{{-- تقرير التصنيع --}}
<li class="nav-item">
    <a class="nav-link" href="{{ route('manufacturing.invoice.report') }}">
        <i class="fas fa-industry"></i>
        <span>تقارير التصنيع</span>
    </a>
</li>
