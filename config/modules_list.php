<?php

return [
    /*
    |--------------------------------------------------------------------------
    | قائمة الموديولات (Modules List)
    |--------------------------------------------------------------------------
    | هنا نقوم بتعريف الموديولات المتاحة في النظام لربطها بكل تينانت.
    | الـ 'key' هو الاسم المستخدم في عمود الـ data التابع للتينانت.
    | الـ 'name' هو الاسم الذي يظهر في لوحة التحكم المركزية.
    | الـ 'sidebar_component' هو اسم الـ blade component الخاص بالـ Sidebar.
    */


    'accounts' => [
        'name' => 'الحسابات (Accounts)',
        'sidebar_components' => ['components.sidebar.accounts', 'components.sidebar.vouchers', 'components.sidebar.journals', 'components.sidebar.transfers'],
    ],

    'inventory' => [
        'name' => 'المخازن والأصناف (Inventory)',
        'sidebar_components' => ['components.sidebar.items'],
    ],

    'manufacturing' => [
        'name' => 'التصنيع (Manufacturing)',
        'sidebar_components' => ['components.sidebar.manufacturing'],
    ],

    'quality' => [
        'name' => 'إدارة الجودة (Quality)',
        'sidebar_components' => ['components.sidebar.quality'],
    ],

    'maintenance' => [
        'name' => 'الصيانة (Maintenance)',
        'sidebar_components' => ['components.sidebar.maintenance'],
    ],

    'fleet' => [
        'name' => 'الأسطول والنقل (Fleet & Shipping)',
        'sidebar_components' => ['components.sidebar.fleet', 'components.sidebar.shipping'],
    ],

    'shipping' => [
        'name' => 'الشحن والتوصيل (Shipping & Delivery)',
        'sidebar_components' => ['components.sidebar.shipping'],
    ],

    'myResources' => [
        'name' => 'أداره الموارد (My Resources)',
        'sidebar_components' => ['components.sidebar.myresources'],
    ],

    'hr' => [
        'name' => 'الموارد البشرية (HR)',
        'sidebar_components' => ['components.sidebar.departments'],
    ],

    'crm' => [
        'name' => 'إدارة علاقات العملاء (CRM)',
        'sidebar_components' => ['components.sidebar.crm'],
    ],

    'rentals' => [
        'name' => 'المستأجرات (Rentals)',
        'sidebar_components' => ['components.sidebar.rentals'],
    ],

    'installments' => [
        'name' => 'الأقساط (Installments)',
        'sidebar_components' => ['components.sidebar.installments'],
    ],

    'inquiries' => [
        'name' => 'الاستفسارات (Inquiries)',
        'sidebar_components' => ['components.sidebar.inquiries'],
    ],

    'checks' => [
        'name' => 'الشيكات (Checks)',
        'sidebar_components' => ['components.sidebar.checks'],
    ],

    'daily_progress' => [
        'name' => 'التقدم اليومي (Daily Progress)',
        'sidebar_components' => ['components.sidebar.daily-progress'],
    ],

    'projects' => [
        'name' => 'المشاريع (Projects & Progress)',
        'sidebar_components' => ['components.sidebar.projects'],
    ],

    'depreciation' => [
        'name' => 'أدارة الأصول (Asset Depreciation)',
        'sidebar_components' => ['components.sidebar.depreciation'],
    ],

    'pos' => [
        'name' => 'نقاط البيع (POS)',
        'sidebar_components' => ['components.sidebar.POS'],
    ],
    'invoices' => [
        'name' => 'الفواتير والمشتريات (Invoices)',
        'sidebar_components' => ['components.sidebar.sales-invoices', 'components.sidebar.purchases-invoices', 'components.sidebar.inventory-invoices'],
    ],
];
