<?php

namespace Database\Seeders;

use App\Enums\DayTypeEnum;
use App\Models\SchoolCalendarDay;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    private const FIXED_HOLIDAYS = [
        '01-07' => 'Coptic Christmas',
        '01-25' => 'January 25 Revolution / Police Day',
        '04-25' => 'Sinai Liberation Day',
        '05-01' => 'Labour Day',
        '06-30' => 'June 30 Revolution',
        '07-23' => 'July 23 Revolution',
        '10-06' => 'Armed Forces Day (October 6)',
    ];

    public function run(): void
    {
        foreach ([2026, 2027] as $year) {
            foreach (self::FIXED_HOLIDAYS as $monthDay => $name) {
                SchoolCalendarDay::updateOrCreate(
                    ['date' => "{$year}-{$monthDay}"],
                    ['type' => DayTypeEnum::Holiday, 'note' => $name]
                );
            }
        }
    }
}
