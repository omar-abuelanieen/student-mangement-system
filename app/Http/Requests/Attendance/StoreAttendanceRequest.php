<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatusEnum;
use App\Rules\IsSchoolDay;
use App\Rules\StudentsBelongToClassroom;
use App\Rules\StudentsNotAlreadyMarked;
use App\Rules\TeacherTeachesClassroom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => [
                'bail', 'required', 'date_format:Y-m-d', 'before_or_equal:today',
                new IsSchoolDay(),
                new StudentsNotAlreadyMarked(),
            ],
            'classroom_id' => [
                'bail', 'required', 'integer',
                Rule::exists('classrooms', 'id')->whereNull('deleted_at'),
                new TeacherTeachesClassroom(),
            ],
            'records' => ['bail', 'required', 'array', 'min:1', new StudentsBelongToClassroom()],
            'records.*.student_profile_id' => ['required', 'integer', 'distinct'],
            'records.*.status' => ['required', Rule::enum(AttendanceStatusEnum::class)],
        ];
    }
}
