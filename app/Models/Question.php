<?php

namespace App\Models;

use App\Enums\QuestionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = ['exam_id', 'body', 'type', 'options', 'correct_answer', 'points'];

    protected function casts(): array
    {
        return [
            'type'    => QuestionTypeEnum::class,
            'options' => 'array',
            'points'  => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
