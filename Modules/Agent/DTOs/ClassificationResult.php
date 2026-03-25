<?php

declare(strict_types=1);

namespace Modules\Agent\DTOs;

/**
 * Classification Result DTO
 *
 * يمثل نتيجة تصنيف نية السؤال وتحديد الـ domain المناسب
 */
class ClassificationResult
{
    /**
     * @param  string|null  $domain  الـ domain المحدد (hr, invoices, inventory, crm) أو null إذا لم يتم التعرف عليه
     * @param  array  $detectedKeywords  الكلمات المفتاحية المكتشفة في السؤال
     * @param  bool  $isMultiIntent  هل السؤال يحتوي على أكثر من نية واحدة
     * @param  float  $confidence  مستوى الثقة في التصنيف (0.0 - 1.0)
     */
    public function __construct(
        public ?string $domain,
        public array $detectedKeywords,
        public bool $isMultiIntent,
        public float $confidence,
    ) {}

    /**
     * التحقق من صحة نتيجة التصنيف
     *
     * النتيجة صالحة إذا:
     * - تم تحديد domain
     * - السؤال ليس متعدد النوايا
     */
    public function isValid(): bool
    {
        return $this->domain !== null && ! $this->isMultiIntent;
    }
}
