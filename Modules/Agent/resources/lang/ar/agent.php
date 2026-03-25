<?php

declare(strict_types=1);

return [
    'title' => 'وكيل الاستعلامات',
    'ask_question' => 'اطرح سؤالك',
    'question_placeholder' => 'اكتب سؤالك هنا... (مثال: كم عدد الموظفين في قسم الموارد البشرية؟)',
    'submit' => 'إرسال',
    'reset' => 'إعادة تعيين',
    'processing' => 'جاري المعالجة...',
    'processing_message' => 'جاري معالجة سؤالك، يرجى الانتظار...',
    'loading' => 'جاري التحميل...',
    'answer' => 'الإجابة',
    'question' => 'السؤال',
    'history' => 'سجل الأسئلة',
    'no_history' => 'لا توجد أسئلة سابقة',
    'no_search_results' => 'لم يتم العثور على نتائج للبحث',
    'character_count' => ':count من 1000 حرف',
    'character_count_of' => 'من',
    'back_to_ask' => 'العودة لطرح سؤال',
    'search' => 'بحث',
    'search_placeholder' => 'ابحث في الأسئلة السابقة...',
    'question_details' => 'تفاصيل السؤال',
    'close' => 'إغلاق',
    'submitted_at' => 'تاريخ الإرسال',
    'domain' => 'المجال',
    'result_count' => 'عدد النتائج',
    'results' => 'نتيجة',
    'processing_time' => 'وقت المعالجة',
    'milliseconds' => 'ميلي ثانية',

    'domains' => [
        'hr' => 'الموارد البشرية',
        'invoices' => 'الفواتير',
        'inventory' => 'المخزون',
        'crm' => 'إدارة العملاء',
    ],

    'validation' => [
        'question_required' => 'يرجى إدخال سؤال',
        'question_string' => 'السؤال يجب أن يكون نصاً',
        'question_min' => 'السؤال يجب أن يكون 5 أحرف على الأقل',
        'question_max' => 'السؤال يجب ألا يتجاوز 1000 حرف',
    ],

    'errors' => [
        'processing_error' => 'حدث خطأ أثناء معالجة سؤالك. يرجى المحاولة مرة أخرى.',
        'database_error' => 'حدث خطأ في قاعدة البيانات. يرجى المحاولة لاحقاً.',
        'query_timeout' => 'استغرق الاستعلام وقتاً طويلاً. يرجى تبسيط سؤالك والمحاولة مرة أخرى.',
        'unmappable_question' => 'عذراً، لم نتمكن من فهم سؤالك. يرجى إعادة صياغته بشكل أوضح.',
        'multi_intent_question' => 'سؤالك يحتوي على أكثر من موضوع. يرجى طرح سؤال واحد في كل مرة.',
        'unauthorized' => 'ليس لديك صلاحية للوصول إلى هذه الميزة.',
        'unsupported_domain' => 'المجال المطلوب غير مدعوم حالياً.',
        'invalid_query_plan' => 'خطة الاستعلام غير صالحة: :errors',
    ],

    'responses' => [
        'no_results' => 'لم يتم العثور على نتائج لسؤالك.',
        'results_found' => 'تم العثور على :count نتيجة:',
        'count_result' => 'العدد: :count',
    ],
];
