<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'level_id',
        'custom_name',
        'section',
        'capacity',
    ];
    public function getTitleAttribute(): string
{
    return $this->custom_name ?? "{$this->level->grade}/{$this->section}";
}
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function students()
    {
        return $this->hasMany(StudentProfile::class);
    }
    public function teachers()
    {
        return $this->hasMany(TeacherProfile::class);
    }
}
