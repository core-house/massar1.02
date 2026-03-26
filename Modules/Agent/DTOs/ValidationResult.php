<?php

declare(strict_types=1);

namespace Modules\Agent\DTOs;

/**
 * Validation Result DTO
 *
 * يمثل نتيجة التحقق من صحة DomainQueryPlan
 * immutable لضمان الأمان
 */
class ValidationResult
{
    /**
     * @param  bool  $isValid  هل الخطة صالحة
     * @param  array  $errors  قائمة رسائل الأخطاء إن وجدت
     * @param  array  $forbiddenColumnsDetected  قائمة الأعمدة المحظورة المكتشفة في الخطة
     */
    public function __construct(
        public readonly bool $isValid,
        public readonly array $errors,
        public readonly array $forbiddenColumnsDetected,
    ) {}
}
