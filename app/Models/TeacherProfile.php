<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'classroom_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

  public function subjects(): BelongsToMany
{
    return $this->belongsToMany(Subject::class, 'subject_teacher_profile')->withTimestamps();
}
public function scheduleEntries()
{
    return $this->hasMany(ScheduleEntry::class);
}
}
