<?php

declare(strict_types=1);

namespace Modules\Rentals\Enums;

enum LeaseStatus: int
{
    case ACTIVE = 1;
    case EXPIRED = 2;
    case TERMINATED = 3;
    case PENDING = 4;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('rentals::rentals.lease_status_active'),
            self::EXPIRED => __('rentals::rentals.lease_status_expired'),
            self::TERMINATED => __('rentals::rentals.lease_status_terminated'),
            self::PENDING => __('rentals::rentals.lease_status_pending'),
        };
    }
}
