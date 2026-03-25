<?php

declare(strict_types=1);

namespace Modules\Agent\DTOs;

/**
 * Typed Filter DTO
 *
 * يمثل filter مكتوب مع نوع محدد للاستخدام في query plans
 * immutable لضمان الأمان
 */
class TypedFilter
{
    /**
     * @param  string  $column  اسم العمود المستهدف
     * @param  string  $operator  المعامل (=, >, <, >=, <=, like, between, in)
     * @param  mixed  $value  القيمة المراد البحث عنها
     * @param  string  $type  نوع الـ filter (exact, like, range, in)
     */
    public function __construct(
        public readonly string $column,
        public readonly string $operator,
        public readonly mixed $value,
        public readonly string $type,
    ) {}

    /**
     * تحويل الـ filter إلى array للتسجيل والتدقيق
     */
    public function toArray(): array
    {
        return [
            'column' => $this->column,
            'operator' => $this->operator,
            'value' => $this->value,
            'type' => $this->type,
        ];
    }
}
