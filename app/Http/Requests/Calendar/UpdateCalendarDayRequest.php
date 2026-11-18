<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\DayTypeEnum;
use App\Models\SchoolCalendarDay;

class UpdateCalendarDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    $day = collect($this->route()->parameters())->first();
    $id  = $day instanceof SchoolCalendarDay ? $day->id : $day;

    return [
        'date' => ['sometimes', 'date_format:Y-m-d', Rule::unique('school_calendar_days', 'date')->ignore($id)],
        'type' => ['sometimes', Rule::enum(DayTypeEnum::class)],
        'note' => ['nullable', 'string', 'max:255'],
    ];
}
}
