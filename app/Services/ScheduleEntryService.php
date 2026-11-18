<?php

namespace App\Services;

use App\Enums\DayOfWeekEnum;
use App\Models\Classroom;
use App\Models\ScheduleEntry;
use App\Models\TeacherProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ScheduleEntryService
{
    private const RELATIONS = ['classroom.level', 'subject', 'teacher.user'];
    private const TEACHER_SCHEDULE_TTL = 3600;

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return ScheduleEntry::with(self::RELATIONS)
            ->orderBy('day')
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function create(array $data): ScheduleEntry
    {
        $entry = ScheduleEntry::create($data);

        $this->forgetTeacherSchedule($entry->teacher_profile_id);

        return $entry->load(self::RELATIONS);
    }

    public function update(ScheduleEntry $entry, array $data): ScheduleEntry
    {
        $oldTeacherId = $entry->teacher_profile_id;

        $entry->update($data);

        $this->forgetTeacherSchedule($oldTeacherId);
        $this->forgetTeacherSchedule($entry->teacher_profile_id);

        return $entry->refresh()->load(self::RELATIONS);
    }

    public function delete(ScheduleEntry $entry): void
    {
        $entry->delete();

        $this->forgetTeacherSchedule($entry->teacher_profile_id);
    }

    public function teacherSchedule(TeacherProfile $teacher): array
    {
        $entries = Cache::remember(
            $this->teacherScheduleKey($teacher->id),
            self::TEACHER_SCHEDULE_TTL,
            fn () => ScheduleEntry::with('classroom.level', 'subject')
                ->where('teacher_profile_id', $teacher->id)
                ->orderBy('start_time')
                ->get()
        );

        return $this->groupByDay($entries);
    }

    public function classroomSchedule(Classroom $classroom): array
    {
        $entries = ScheduleEntry::with('subject', 'teacher.user')
            ->where('classroom_id', $classroom->id)
            ->orderBy('start_time')
            ->get();

        return $this->groupByDay($entries);
    }

    private function groupByDay(Collection $entries): array
    {
        $byDay = $entries->groupBy(fn (ScheduleEntry $entry) => $entry->day->value);

        return collect(DayOfWeekEnum::schoolDays())
            ->mapWithKeys(fn (DayOfWeekEnum $day) => [
                $day->value => $byDay->get($day->value, collect()),
            ])
            ->all();
    }

    private function forgetTeacherSchedule(int $teacherId): void
    {
        Cache::forget($this->teacherScheduleKey($teacherId));
    }

    private function teacherScheduleKey(int $teacherId): string
    {
        return "teacher_schedule:{$teacherId}";
    }
}
