<?php

namespace App\Http\Requests\Schedule;

use App\Enums\DayOfWeekEnum;
use App\Rules\TeacherTeachesSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'end_time'           => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
}
