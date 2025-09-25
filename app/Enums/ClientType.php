<?php

namespace App\Enums;

enum ClientType: int
{
    case Person = 1;
    case Company = 2;
    case MainContractor = 3;
    case Consultant = 4;
    case Owner = 5;
    case ENGINEER = 6;

    public function label(): string
    {
        return match ($this) {
            self::Person => 'شخص',
            self::Company => 'شركة',
            self::MainContractor => 'مقاول رئيسي',
            self::Consultant => 'استشاري',
            self::Owner => 'مالك',
            self::ENGINEER => 'مهندس',
        };
    }
}
