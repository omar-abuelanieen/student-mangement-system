<?php

namespace App\Enums;

enum DayOfWeekEnum: string
{
    case Saturday  = 'saturday';
    case Sunday    = 'sunday';
    case Monday    = 'monday';
    case Tuesday   = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday  = 'thursday';
    case Friday    = 'friday';

    public function isWeekend(): bool
    {
        return in_array($this, config('school.weekend_days'), true);
    }

    public static function schoolDays(): array
    {
        return array_values(array_filter(self::cases(), fn (self $day) => ! $day->isWeekend()));
    }
}
