<?php

namespace Modules\Manufacturing\Enums;

enum ManufacturingStageStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::IN_PROGRESS => 'جاري التنفيذ',
            self::COMPLETED => 'مكتمل',
            self::ON_HOLD => 'معلق',
            self::CANCELLED => 'ملغي',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::ON_HOLD => 'secondary',
            self::CANCELLED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'la-clock',
            self::IN_PROGRESS => 'la-cog',
            self::COMPLETED => 'la-check-circle',
            self::ON_HOLD => 'la-pause-circle',
            self::CANCELLED => 'la-times-circle',
        };
    }
}
