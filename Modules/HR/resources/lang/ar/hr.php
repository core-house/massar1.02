<?php

declare(strict_types=1);

return [
    // General
    'home' => 'الرئيسية',
    'departments' => 'الأقسام',
    
    // Departments
    'add_new_department' => 'إضافة قسم جديد',
    'search_by_title' => 'البحث بالعنوان',
    'department_name' => 'اسم القسم',
    'parent' => 'القسم الأب',
    'director' => 'المدير',
    'deputy_director' => 'نائب المدير',
    'description' => 'الوصف',
    'max_leave_percentage' => 'الحد الأقصى لنسبة الإجازات',
    'actions' => 'الإجراءات',
    'view_hierarchy' => 'عرض الهيكل التنظيمي',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    
    // Modal Titles
    'add_department' => 'إضافة قسم',
    'edit_department' => 'تعديل قسم',
    'hierarchy' => 'الهيكل التنظيمي',
    
    // Hierarchy
    'parents' => 'الأقسام الأعلى',
    'current_department' => 'القسم الحالي',
    'children_departments' => 'الأقسام الفرعية',
    'no_child_departments' => 'لا توجد أقسام فرعية',
    'no_department_selected' => 'لم يتم اختيار قسم',
    
    // Form Labels
    'title' => 'العنوان',
    'select_parent' => 'اختر القسم الأب',
    'select_director' => 'اختر المدير',
    'select_deputy_director' => 'اختر نائب المدير',
    'no_employees_found' => 'لا يوجد موظفين في هذا القسم',
    
    // Form
    'max_leave_percentage_placeholder' => 'أدخل النسبة المئوية (اختياري)',
    'max_leave_percentage_help' => 'الحد الأقصى لنسبة الموظفين المسموح لهم بالإجازة في نفس الوقت',
    'company_percentage_info' => 'نسبة الشركة الحالية: :percentage%',
    'company_percentage_not_set_warning' => 'تحذير: لم يتم تعيين نسبة الشركة في الإعدادات',
    
    // Buttons
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'update' => 'تحديث',
    'close' => 'إغلاق',
    
    // Messages
    'no_departments_found' => 'لا توجد أقسام',
    'confirm_delete_department' => 'هل أنت متأكد من حذف هذا القسم؟',
    'department_created_successfully' => 'تم إنشاء القسم بنجاح',
    'department_updated_successfully' => 'تم تحديث القسم بنجاح',
    'department_deleted_successfully' => 'تم حذف القسم بنجاح',
    
    // Validation
    'department_percentage_requires_company_percentage' => 'يجب تعيين نسبة الشركة أولاً في إعدادات الموارد البشرية قبل تعيين نسبة القسم',
    'department_percentage_exceeds_company' => 'نسبة القسم (:department_percentage%) لا يمكن أن تتجاوز نسبة الشركة (:company_percentage%)',
];
