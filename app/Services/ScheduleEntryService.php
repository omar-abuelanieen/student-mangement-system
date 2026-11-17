<?php

namespace App\Services;

use App\Models\ScheduleEntry;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleEntryService
{
    private const RELATIONS = ['classroom.level', 'subject', 'teacher.user'];

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return ScheduleEntry::with(self::RELATIONS)
            ->orderBy('day')
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function create(array $data): ScheduleEntry
    {
        return ScheduleEntry::create($data)->load(self::RELATIONS);
    }

    public function update(ScheduleEntry $entry, array $data): ScheduleEntry
    {
        $entry->update($data);

        return $entry->refresh()->load(self::RELATIONS);
    }

    public function delete(ScheduleEntry $entry): void
    {
        $entry->delete();
    }
}
