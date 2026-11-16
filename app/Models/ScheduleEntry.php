<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'classroom_id',
        'subject_id',
        'teacher_id',
        'date',
        'start_time',
        'end_time',
    ];
}
