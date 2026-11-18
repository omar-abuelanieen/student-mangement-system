<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\DayTypeEnum;

class StoreCalendarDayRequest extends FormRequest
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
    return [
        'date' => ['required', 'date_format:Y-m-d', 'unique:school_calendar_days,date'],
        'type' => ['required', Rule::enum(DayTypeEnum::class)],
        'note' => ['nullable', 'string', 'max:255'],
    ];
}
}
