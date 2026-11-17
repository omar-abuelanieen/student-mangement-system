<?php

namespace App\Models;

use App\Enums\DayOfWeekEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleEntry extends Model
{
    protected $fillable = [
        'classroom_id',
        'subject_id',
        'teacher_profile_id',
        'day',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'day' => DayOfWeekEnum::class,
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }
}
