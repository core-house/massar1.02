<?php

namespace Modules\Inquiries\Enums;

enum QuotationStateEnum: string
{
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case RE_ESTIMATION = 'Re-estimation';
    case NEGLECTED = 'Neglected';
    case PROJECT_POSTPONEMENT = 'Project Postponement';

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
