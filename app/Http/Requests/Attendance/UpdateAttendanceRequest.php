<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatusEnum;
use App\Models\ScheduleEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendanceRequest extends FormRequest
{
    // المدرس لازم يكون عنده حصة مع فصل السجل ده
    public function authorize(): bool
    {
        $attendance = $this->route('attendance');

        return ScheduleEntry::where('classroom_id', $attendance->classroom_id)
            ->whereHas('teacher', fn ($query) => $query->where('user_id', $this->user()->id))
            ->exists();
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::enum(AttendanceStatusEnum::class)],
        ];
    }
}
