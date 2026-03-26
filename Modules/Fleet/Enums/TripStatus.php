<?php

declare(strict_types=1);

namespace Modules\Fleet\Enums;

enum TripStatus: string
{
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => __('fleet::fleet.scheduled'),
            self::IN_PROGRESS => __('fleet::fleet.in_progress'),
            self::COMPLETED => __('fleet::fleet.completed'),
            self::CANCELLED => __('fleet::fleet.cancelled'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SCHEDULED => 'info',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
