<?php

namespace App\Enums;

enum QuestionTypeEnum: string
{
    case MultipleChoice = 'mcq';
    case TrueFalse      = 'true_false';
    case ShortAnswer    = 'short_answer';
}
