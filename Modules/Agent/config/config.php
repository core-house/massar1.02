<?php

declare(strict_types=1);

return [
    'name' => 'Agent',

    /*
    |--------------------------------------------------------------------------
    | Agent Module Domain Configurations
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحتوي على تكوينات الـ domains المختلفة التي يدعمها موديول Agent.
    | كل domain يحتوي على:
    | - keywords: الكلمات المفتاحية للتصنيف
    | - tables: الجداول المسموحة مع الأعمدة المصرح بها
    |
    */

    'domains' => [
        'hr' => [
            'keywords' => ['موظف', 'موظفين', 'عامل', 'عمال', 'موظفي', 'موظفة', 'موظفات'],
            'tables' => [
                'employees' => [
                    'model' => \Modules\HR\Models\Employee::class,
                    'allowed_columns' => [
                        'id',
                        'name',
                        'email',
                        'department_id',
                        'position',
                        'hire_date',
                        'status',
                        'created_at',
                    ],
                    'searchable_columns' => [
                        'name',
                        'email',
                        'position',
                    ],
                    'forbidden_columns' => [
                        'password',
                        'salary',
                        'ssn',
                        'bank_account',
                        'remember_token',
                    ],
                    'required_scopes' => ['company', 'branch'],
                    'relationships' => ['department'],
                ],
            ],
        ],

        'invoices' => [
            'keywords' => ['فاتورة', 'فواتير', 'فاتوره', 'فواتير'],
            'tables' => [
                'invoices' => [
                    'model' => \Modules\Invoices\Models\Invoice::class,
                    'allowed_columns' => [
                        'id',
                        'invoice_number',
                        'client_name',
                        'total',
                        'status',
                        'date',
                        'created_at',
                    ],
                    'searchable_columns' => [
                        'invoice_number',
                        'client_name',
                        'status',
                    ],
                    'forbidden_columns' => [
                        'internal_notes',
                        'commission',
                        'discount_reason',
                    ],
                    'required_scopes' => ['company', 'branch'],
                    'relationships' => ['client', 'items'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Execution Limits
    |--------------------------------------------------------------------------
    |
    | الحدود القصوى لتنفيذ الاستعلامات
    |
    */

    'limits' => [
        'max_results' => 100,
        'max_execution_time_ms' => 5000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Classification Settings
    |--------------------------------------------------------------------------
    |
    | إعدادات تصنيف الأسئلة
    |
    */

    'classification' => [
        'min_confidence' => 0.5,
        'multi_intent_threshold' => 2,
    ],
];
