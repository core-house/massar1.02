<?php

namespace Modules\CRM\Enums;

enum ActivityTypeEnum: int
{
    case CALL = 0;      // مكالمة
    case MESSAGE = 1;   // رسالة
    case MEETING = 2;   // اجتماع

    public function label(): string
    {
        return match ($this) {
            self::CALL => __('crm::crm.call'),
            self::MESSAGE => __('crm::crm.message'),
            self::MEETING => __('crm::crm.meeting'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
