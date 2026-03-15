<?php

namespace Modules\CRM\Enums;

enum TaskPriorityEnum: string
{
    case HIGH = 'عاجلة';
    case MEDIUM = 'متوسطة';
    case LOW = 'غير عاجلة';

    public function label(): string
    {
        return match ($this) {
            self::HIGH => __('crm::crm.high'),
            self::MEDIUM => __('crm::crm.medium'),
            self::LOW => __('crm::crm.low'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HIGH => 'danger',     // أحمر
            self::MEDIUM => 'warning',  // أصفر
            self::LOW => 'success',     // أخضر
        };
    }
}
