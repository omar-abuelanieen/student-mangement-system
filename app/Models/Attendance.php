<?php

namespace App\Models;

use App\Enums\AttendanceStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_profile_id',
        'classroom_id',
        'marked_by',
        'date',
        'status',
        'marked_at',
    ];

    protected function casts(): array
    {
        return [
            'date'      => 'date',
            'status'    => AttendanceStatusEnum::class,
            'marked_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'marked_by');
    }
}
