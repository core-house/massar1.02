<?php

namespace Modules\Manufacturing\Enums;

enum ManufacturingStageStatus: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case STOPPED = 'stopped';

    public function label(): string
    {
        return match ($this) {
            self::IN_PROGRESS => __('manufacturing::manufacturing.in_progress'),
            self::COMPLETED => __('manufacturing::manufacturing.completed'),
            self::STOPPED => __('manufacturing::manufacturing.stopped'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::STOPPED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'la-cog',
            self::COMPLETED => 'la-check-circle',
            self::STOPPED => 'la-pause-circle',
        };
    }
}
