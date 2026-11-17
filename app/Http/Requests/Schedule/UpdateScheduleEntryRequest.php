<?php

namespace App\Http\Requests\Schedule;

use App\Enums\DayOfWeekEnum;
use App\Models\ScheduleEntry;
use App\Rules\NoScheduleOverlap;
use App\Rules\TeacherTeachesSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScheduleEntryRequest extends FormRequest
{
    private ?ScheduleEntry $currentEntry = null;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $entry = collect($this->route()->parameters())->first();

        if (! $entry instanceof ScheduleEntry) {
            $entry = ScheduleEntry::findOrFail($entry);
        }

        $this->currentEntry = $entry;

        $this->mergeIfMissing([
            'classroom_id'       => $entry->classroom_id,
            'teacher_profile_id' => $entry->teacher_profile_id,
            'subject_id'         => $entry->subject_id,
            'day'                => $entry->day->value,
            'start_time'         => substr($entry->start_time, 0, 5),
            'end_time'           => substr($entry->end_time, 0, 5),
        ]);
    }

    public function rules(): array
    {
        return [
            'classroom_id'       => ['required', 'integer', Rule::exists('classrooms', 'id')->whereNull('deleted_at')],
            'teacher_profile_id' => ['required', 'integer', Rule::exists('teacher_profiles', 'id')->whereNull('deleted_at')],
            'subject_id'         => [
                'required', 'integer',
                Rule::exists('subjects', 'id')->whereNull('deleted_at'),
                new TeacherTeachesSubject(),
            ],
            'day'                => ['required', Rule::enum(DayOfWeekEnum::class)->only(DayOfWeekEnum::schoolDays())],
            'start_time'         => ['required', 'date_format:H:i'],
            'end_time'           => [
                'bail', 'required', 'date_format:H:i', 'after:start_time',
                new NoScheduleOverlap($this->currentEntry?->id),
            ],
        ];
    }
}
