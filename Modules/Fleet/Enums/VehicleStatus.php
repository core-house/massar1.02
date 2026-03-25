<?php

declare(strict_types=1);

namespace Modules\Fleet\Enums;

enum VehicleStatus: string
{
    case AVAILABLE = 'available';
    case IN_USE = 'in_use';
    case MAINTENANCE = 'maintenance';
    case OUT_OF_SERVICE = 'out_of_service';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => __('fleet::fleet.available'),
            self::IN_USE => __('fleet::fleet.in_use'),
            self::MAINTENANCE => __('fleet::fleet.maintenance'),
            self::OUT_OF_SERVICE => __('fleet::fleet.out_of_service'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::IN_USE => 'primary',
            self::MAINTENANCE => 'warning',
            self::OUT_OF_SERVICE => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
