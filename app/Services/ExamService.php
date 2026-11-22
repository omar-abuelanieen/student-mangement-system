<?php

namespace App\Services;

use App\Enums\QuestionTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Exam;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function list(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = Exam::query()
            ->with('subject', 'classroom.level', 'teacher.user')
            ->withCount('questions')
            ->latest('id');

        if ($user->hasRole(RoleEnum::Teacher->value)) {
            $query->where('teacher_profile_id', TeacherProfile::where('user_id', $user->id)->value('id'));
        } elseif ($user->hasRole(RoleEnum::Student->value)) {
            $query->where('classroom_id', StudentProfile::where('user_id', $user->id)->value('classroom_id') ?? 0);
        }

        return $query->paginate($perPage);
    }

    public function create(int $userId, array $data): Exam
    {
        $teacherId = TeacherProfile::where('user_id', $userId)->value('id');

        return DB::transaction(function () use ($teacherId, $data) {
            $exam = Exam::create([
                'teacher_profile_id' => $teacherId,
                'classroom_id'       => $data['classroom_id'],
                'subject_id'         => $data['subject_id'],
                'title'              => $data['title'],
            ]);

            $exam->questions()->createMany(
                collect($data['questions'])->map(fn (array $question) => [
                    'body'           => $question['body'],
                    'type'           => $question['type'],
                    'options'        => $question['type'] === QuestionTypeEnum::MultipleChoice->value
                        ? array_values($question['options'])
                        : null,
                    'correct_answer' => $question['type'] === QuestionTypeEnum::TrueFalse->value
                        ? strtolower($question['correct_answer'])
                        : $question['correct_answer'],
                    'points'         => $question['points'] ?? 1,
                ])->all()
            );

            return $this->loadDetails($exam);
        });
    }

    public function updateTitle(Exam $exam, string $title): Exam
    {
        $exam->update(['title' => $title]);

        return $this->loadDetails($exam->refresh())->loadCount('questions');
    }

    public function delete(Exam $exam): void
    {
        $exam->delete();
    }

    public function loadDetails(Exam $exam): Exam
    {
        return $exam->load('questions', 'subject', 'classroom.level', 'teacher.user');
    }
}
