<?php

namespace App\Models;

use App\Enums\SubmissionStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSubmission extends Model
{
    protected $fillable = [
        'exam_id', 'student_profile_id', 'status', 'score',
        'submitted_at', 'graded_at', 'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'status'       => SubmissionStatusEnum::class,
            'score'        => 'decimal:2',
            'submitted_at' => 'datetime',
            'graded_at'    => 'datetime',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'graded_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SubmissionAnswer::class);
    }
}
