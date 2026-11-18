<?php

namespace App\Enums;

enum DayTypeEnum: string
{
    case SchoolDay = 'school_day';
    case Weekend   = 'weekend';
    case Holiday   = 'holiday';
}
