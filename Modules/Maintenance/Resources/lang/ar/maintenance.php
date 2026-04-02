<?php

declare(strict_types=1);

return [
    // General
    'maintenance'               => 'الصيانة',
    'maintenances'              => 'طلبات الصيانة',
    'add_new'                   => 'إضافة جديد',
    'save'                      => 'حفظ',
    'update'                    => 'تحديث',
    'edit'                      => 'تعديل',
    'delete'                    => 'حذف',
    'back'                      => 'رجوع',
    'cancel'                    => 'إلغاء',
    'print'                     => 'طباعة',
    'actions'                   => 'الإجراءات',
    'name'                      => 'الاسم',
    'description'               => 'الوصف',
    'notes'                     => 'ملاحظات',
    'status'                    => 'الحالة',
    'date'                      => 'التاريخ',
    'branch'                    => 'الفرع',
    'no_data_available'         => 'لا توجد بيانات',
    'are_you_sure_delete'       => 'هل أنت متأكد من حذف هذا العنصر؟',
    'export_excel'              => 'تصدير Excel',
    'export_pdf'                => 'تصدير PDF',
    'create'                    => 'إنشاء',
    'n_a'                       => 'غير متوفر',

    // Client
    'client_name'               => 'اسم العميل',
    'client_phone'              => 'هاتف العميل',

    // Item
    'item_name'                 => 'اسم البند',
    'item_number'               => 'رقم البند',

    // Service Type
    'service_type'              => 'نوع الخدمة',
    'service_types'             => 'أنواع الخدمات',
    'add_new_service_type'      => 'إضافة نوع خدمة جديد',
    'edit_service_type'         => 'تعديل نوع الخدمة',
    'service_type_details'      => 'تفاصيل نوع الخدمة',
    'service_type_information'  => 'معلومات نوع الخدمة',
    'choose_service_type'       => 'اختر نوع الخدمة',
    'enter_name'                => 'أدخل الاسم',
    'enter_description'         => 'أدخل الوصف',

    // Status
    'choose_status'             => 'اختر الحالة',
    'pending'                   => 'قيد الانتظار',
    'in_progress'               => 'قيد التنفيذ',
    'completed'                 => 'مكتملة',
    'cancelled'                 => 'ملغاة',
    'active'                    => 'نشط',
    'inactive'                  => 'غير نشط',
    'overdue'                   => 'متأخر',
    'due_soon'                  => 'قريب الموعد',
    'not_done_yet'              => 'لم تتم بعد',

    // Dates
    'accural_date'              => 'تاريخ الاستحقاق',
    'start_date'                => 'تاريخ البداية',
    'next_maintenance_date'     => 'تاريخ الصيانة القادمة',
    'last_maintenance_date'     => 'تاريخ آخر صيانة',

    // Costs
    'spare_parts_cost'          => 'تكلفة قطع الغيار',
    'labor_cost'                => 'تكلفة العمالة',
    'total_cost'                => 'التكلفة الإجمالية',

    // Asset
    'asset'                     => 'الأصل',
    'asset_accounting'          => 'الأصل (محاسبي)',
    'asset_direct'              => 'الأصل (مباشر)',
    'choose_asset'              => 'اختر الأصل',
    'accounting'                => 'محاسبي',
    'direct'                    => 'مباشر',

    // Maintenance Type
    'maintenance_type'          => 'نوع الصيانة',
    'choose_type'               => 'اختر النوع',
    'periodic'                  => 'دورية',
    'emergency'                 => 'طارئة',
    'repair'                    => 'إصلاح',

    // Maintenance Details
    'maintenance_details'       => 'تفاصيل الصيانة',
    'maintenance_information'   => 'معلومات الصيانة',
    'add_new_maintenance'       => 'إضافة جديد',

    // Periodic Maintenance
    'periodic_maintenance'              => 'الصيانة الدورية',
    'periodic_maintenance_details'      => 'تفاصيل الصيانة الدورية',
    'periodic_maintenance_information'  => 'معلومات الصيانة الدورية',
    'add_periodic_maintenance'          => 'إضافة جدول صيانة دورية',
    'add_new_periodic_schedule'         => 'إضافة جدول صيانة دورية جديد',
    'edit_periodic_schedule'            => 'تعديل جدول الصيانة الدورية',
    'create_from_schedule'              => 'إنشاء صيانة من الجدول',
    'create_from_schedule_title'        => 'إنشاء من الجدول',
    'no_periodic_schedules'             => 'لا توجد جداول صيانة دورية',

    // Frequency
    'frequency_type'            => 'نوع التكرار',
    'frequency_value_days'      => 'قيمة التكرار (أيام)',
    'choose_frequency_type'     => 'اختر نوع التكرار',
    'frequency'                 => 'التكرار',
    'daily'                     => 'يومي',
    'weekly'                    => 'أسبوعي',
    'monthly'                   => 'شهري',
    'quarterly'                 => 'ربع سنوي',
    'semi_annual'               => 'نصف سنوي',
    'annual'                    => 'سنوي',
    'custom_days'               => 'عدد أيام مخصص',

    // Periodic fields
    'notification_days_before'  => 'أيام الإشعار المسبق',
    'is_active'                 => 'نشط',
    'next_maintenance'          => 'الصيانة القادمة',
    'last_maintenance'          => 'آخر صيانة',
    'client'                    => 'العميل',
    'item'                      => 'البند',
    'toggle_status'             => 'تبديل الحالة',
    'choose_branch'             => 'اختر الفرع',

    // Dashboard
    'dashboard'                 => 'لوحة تحكم إدارة الصيانة',
    'dashboard_subtitle'        => 'نظرة شاملة على طلبات الصيانة والأداء',
    'total_requests'            => 'إجمالي الطلبات',
    'this_week'                 => 'هذا الأسبوع',
    'urgent'                    => 'عاجل',
    'monthly_performance'       => 'الأداء الشهري',
    'current_month'             => 'الشهر الحالي',
    'last_month'                => 'الشهر الماضي',
    'change_rate'               => 'نسبة التغيير',
    'status_distribution'       => 'توزيع الحالات',
    'performance_indicators'    => 'مؤشرات الأداء',
    'completion_rate'           => 'معدل الإنجاز',
    'avg_completion_days'       => 'متوسط أيام الإنجاز',
    'service_type_stats'        => 'إحصائيات أنواع الصيانة',
    'total_requests_col'        => 'إجمالي الطلبات',
    'no_service_types'          => 'لا توجد أنواع صيانة مسجلة',
    'recent_maintenances'       => 'أحدث طلبات الصيانة',
    'view_all'                  => 'عرض الكل',
    'client_name_col'           => 'اسم العميل',
    'item_col'                  => 'البند',
    'item_number_col'           => 'رقم البند',
    'service_type_col'          => 'نوع الصيانة',
    'no_maintenances'           => 'لا توجد طلبات صيانة',
    'total_requests_chart'      => 'إجمالي الطلبات',
    'completed_chart'           => 'مكتملة',

    // Dashboard Charts
    'monthly_trend_chart'       => 'اتجاه طلبات الصيانة الشهري',
    'service_types_distribution'=> 'توزيع أنواع الصيانة',
    'status_distribution_chart' => 'توزيع حالات الطلبات',
    'top_service_types_chart'   => 'أفضل أنواع الصيانة (حسب عدد الطلبات)',
    'pending_chart'             => 'قيد الانتظار',
    'in_progress_chart'         => 'قيد التنفيذ',
    'request_count'             => 'عدد الطلبات',
    'requests_count_label'      => 'عدد الطلبات: ',
    'day_label'                 => 'يوم',

    // Periodic Show
    'periodic_maintenance_info' => 'معلومات الصيانة الدورية',
];
