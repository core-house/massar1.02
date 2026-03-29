<?php

declare(strict_types=1);

namespace Modules\Rentals\Enums;

enum UnitStatus: int
{
    case AVAILABLE = 1;
    case RENTED = 2;
    case MAINTENANCE = 3;

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => __('rentals::rentals.status_available'),
            self::RENTED => __('rentals::rentals.status_rented'),
            self::MAINTENANCE => __('rentals::rentals.status_maintenance'),
        };
    }
}
