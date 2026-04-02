<?php

namespace Modules\MyResources\Enums;

enum AssignmentType: string
{
    case CURRENT = 'current';
    case UPCOMING = 'upcoming';
    case PAST = 'past';

    public function label(): string
    {
        return match ($this) {
            self::CURRENT  => __('myresources.current'),
            self::UPCOMING => __('myresources.upcoming'),
            self::PAST     => __('myresources.historical'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}

