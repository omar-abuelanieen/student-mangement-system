<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin    = 'admin';
    case Manager  = 'manager';
    case Teacher  = 'teacher';
    case Guardian = 'guardian';
    case Student  = 'student';
}
