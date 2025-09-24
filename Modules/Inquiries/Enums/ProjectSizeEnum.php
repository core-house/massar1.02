<?php

namespace Modules\Inquiries\Enums;

enum ProjectSizeEnum: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';

    public function label(): string
    {
        return match ($this) {
            self::A => 'A',
            self::B => 'B',
            self::C => 'C',
            self::D => 'D',
            self::E => 'E',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
