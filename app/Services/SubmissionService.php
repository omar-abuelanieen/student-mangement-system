<?php

namespace App\Services;

use App\Enums\SubmissionStatusEnum;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Question;
use App\Models\StudentProfile;
use App\Models\SubmissionAnswer;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function listForExam(Exam $exam, int $perPage = 15): LengthAwarePaginator
    {
        return ExamSubmission::with('student.user')
            ->where('exam_id', $exam->id)
            ->latest('id')
            ->paginate($perPage);
    }

    public function submit(User $user, Exam $exam, array $answers): ExamSubmission
    {
        $studentId = StudentProfile::where('user_id', $user->id)->value('id');
        $given     = collect($answers)->keyBy('question_id');
        $questions = Question::where('exam_id', $exam->id)->pluck('id');

        try {
            return DB::transaction(function () use ($exam, $studentId, $given, $questions) {
                $now = now();

                $submission = ExamSubmission::create([
                    'exam_id'            => $exam->id,
                    'student_profile_id' => $studentId,
                    'status'             => SubmissionStatusEnum::Submitted,
                    'submitted_at'       => $now,
                ]);

                // صف لكل سؤال، حتى اللي الطالب ما جاوبش عليه (answer = null)
                SubmissionAnswer::insert($questions->map(fn ($questionId) => [
                    'exam_submission_id' => $submission->id,
                    'question_id'        => $questionId,
                    'answer'             => $given->get($questionId)['answer'] ?? null,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ])->all());

                return $this->loadDetails($submission);
            });
        } catch (UniqueConstraintViolationException) {
            // unique(exam_id, student_profile_id): يحمي حتى لو طلبين اتبعتوا مع بعض
            throw ValidationException::withMessages([
                'exam' => ['You have already submitted this exam.'],
            ]);
        }
    }

    public function grade(User $user, ExamSubmission $submission, array $grades): ExamSubmission
    {
        $teacherId = TeacherProfile::where('user_id', $user->id)->value('id');

        return DB::transaction(function () use ($submission, $grades, $teacherId) {
            // قفل على التسليم: تصحيحين متزامنين مبيدوسوش على بعض
            $locked = ExamSubmission::whereKey($submission->id)->lockForUpdate()->firstOrFail();

            $existing = SubmissionAnswer::where('exam_submission_id', $locked->id)->get()->keyBy('question_id');
            $now      = now();

            SubmissionAnswer::upsert(
                collect($grades)->map(fn (array $grade) => [
                    'exam_submission_id' => $locked->id,
                    'question_id'        => $grade['question_id'],
                    'is_correct'         => (bool) $grade['is_correct'],
                    // لو الشرح مش مبعوت في إعادة التصحيح نسيب القديم
                    'explanation'        => array_key_exists('explanation', $grade)
                        ? $grade['explanation']
                        : $existing->get($grade['question_id'])?->explanation,
                    'updated_at'         => $now,
                ])->all(),
                ['exam_submission_id', 'question_id'],
                ['is_correct', 'explanation', 'updated_at'],
            );

            // الدرجة من الداتابيز: صح لكل الأسئلة، مش بس اللي اتبعتت
            $score = SubmissionAnswer::where('submission_answers.exam_submission_id', $locked->id)
                ->where('submission_answers.is_correct', true)
                ->join('questions', 'questions.id', '=', 'submission_answers.question_id')
                ->sum('questions.points');

            $locked->update([
                'score'     => $score,
                'status'    => SubmissionStatusEnum::Graded,
                'graded_at' => $now,
                'graded_by' => $teacherId,
            ]);

            return $this->loadDetails($locked);
        });
    }

    public function loadDetails(ExamSubmission $submission): ExamSubmission
    {
        return $submission->load('exam', 'student.user', 'answers.question');
    }
}
