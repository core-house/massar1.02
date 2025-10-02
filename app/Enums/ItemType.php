<?php

namespace App\Enums;

enum ItemType: int
{
    case Inventory = 1;
    case Service = 2;

    public function label(): string
    {
        return match ($this) {
            self::Inventory => 'مخزنى',
            self::Service => 'خدمة',
        };
    }
}

