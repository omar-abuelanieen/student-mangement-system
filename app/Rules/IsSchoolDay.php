<?php

namespace App\Rules;

use App\Services\SchoolCalendarService;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsSchoolDay implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::createFromFormat('Y-m-d', $value);

        if (! app(SchoolCalendarService::class)->isSchoolDay($date)) {
            $fail('The selected date is not a school day (weekend or holiday).');
        }
    }
}
