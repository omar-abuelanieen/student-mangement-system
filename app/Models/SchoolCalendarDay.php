<?php

namespace App\Models;

use App\Enums\DayTypeEnum;
use Illuminate\Database\Eloquent\Model;

class SchoolCalendarDay extends Model
{
    protected $fillable = ['date', 'type', 'note'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => DayTypeEnum::class,
        ];
    }
}
