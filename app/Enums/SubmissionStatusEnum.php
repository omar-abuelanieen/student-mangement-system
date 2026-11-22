<?php

namespace App\Enums;

enum SubmissionStatusEnum: string
{
    case Submitted = 'submitted';
    case Graded    = 'graded';
}
