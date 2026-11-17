<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
