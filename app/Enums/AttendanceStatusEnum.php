<?php

namespace App\Enums;

enum AttendanceStatusEnum: string
{
    case Present = 'present';
    case Absent  = 'absent';
    case Late    = 'late';
    case Excused = 'excused';
}
