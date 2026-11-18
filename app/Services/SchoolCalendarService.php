<?php

namespace App\Services;

use App\Enums\DayOfWeekEnum;
use App\Enums\DayTypeEnum;
use App\Models\SchoolCalendarDay;
use Carbon\CarbonInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SchoolCalendarService
{
    public function list(?int $year = null, int $perPage = 15): LengthAwarePaginator
    {
        return SchoolCalendarDay::query()
            ->when($year, fn ($query) => $query->whereYear('date', $year))
            ->orderBy('date')
            ->paginate($perPage);
    }

    public function create(array $data): SchoolCalendarDay
    {
        return SchoolCalendarDay::create($data);
    }

    public function update(SchoolCalendarDay $day, array $data): SchoolCalendarDay
    {
        $day->update($data);

        return $day->refresh();
    }

    public function delete(SchoolCalendarDay $day): void
    {
        $day->delete();
    }

    public function isSchoolDay(CarbonInterface $date): bool
    {
        $override = SchoolCalendarDay::whereDate('date', $date->toDateString())->first();

        if ($override) {
            return $override->type === DayTypeEnum::SchoolDay;
        }

        return ! DayOfWeekEnum::from(strtolower($date->englishDayOfWeek))->isWeekend();
    }
}
