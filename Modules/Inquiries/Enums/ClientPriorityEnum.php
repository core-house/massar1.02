<?php

namespace Modules\Inquiries\Enums;

enum ClientPriorityEnum: string
{
    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'غير عاجلة',
            self::MEDIUM => 'متوسطة',
            self::HIGH => 'عاجلة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'success',  // أخضر
            self::MEDIUM => 'warning', // أصفر
            self::HIGH => 'danger',   // أحمر
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
