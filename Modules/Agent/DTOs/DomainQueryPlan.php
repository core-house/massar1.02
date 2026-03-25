<?php

declare(strict_types=1);

namespace Modules\Agent\DTOs;

/**
 * Domain Query Plan DTO
 *
 * يمثل خطة استعلام مكتوبة وقابلة للتدقيق قبل تنفيذ أي query
 * الخطة immutable بعد الإنشاء لضمان الأمان
 */
class DomainQueryPlan
{
    /**
     * @param  string  $domain  الـ domain المستهدف (hr, invoices, inventory, crm)
     * @param  string  $table  اسم الجدول المستهدف
     * @param  string  $operation  نوع العملية (select, count, aggregate)
     * @param  array  $allowedColumns  قائمة الأعمدة المصرح بها للاستعلام
     * @param  array  $typedFilters  قائمة الـ filters المطبقة (TypedFilter[])
     * @param  array|null  $sort  معايير الترتيب ['column' => 'direction']
     * @param  int  $limit  الحد الأقصى للنتائج (max 100)
     */
    public function __construct(
        public readonly string $domain,
        public readonly string $table,
        public readonly string $operation,
        public readonly array $allowedColumns,
        public readonly array $typedFilters,
        public readonly ?array $sort,
        public readonly int $limit,
    ) {}

    /**
     * تحويل الخطة إلى array للتسجيل والتدقيق
     */
    public function toArray(): array
    {
        return [
            'domain' => $this->domain,
            'table' => $this->table,
            'operation' => $this->operation,
            'allowed_columns' => $this->allowedColumns,
            'typed_filters' => array_map(fn ($f) => $f->toArray(), $this->typedFilters),
            'sort' => $this->sort,
            'limit' => $this->limit,
        ];
    }
}
