<?php

namespace Modules\Inquiries\Enums;

enum QuotationStateEnum: string
{
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case RE_ESTIMATION = 'RE_ESTIMATION';
    case NEGLECTED = 'NEGLECTED';
    case PROJECT_POSTPONEMENT = 'PROJECT_POSTPONEMENT';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'مقبول',
            self::REJECTED => 'مرفوض',
            self::RE_ESTIMATION => 'إعادة تقدير',
            self::NEGLECTED => 'مهمل',
            self::PROJECT_POSTPONEMENT => 'تأجيل المشروع',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::APPROVED => 'success',   // أخضر
            self::REJECTED => 'danger',   // أحمر
            self::RE_ESTIMATION => 'warning', // أصفر
            self::NEGLECTED => 'secondary', // رمادي
            self::PROJECT_POSTPONEMENT => 'info', // أزرق
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
