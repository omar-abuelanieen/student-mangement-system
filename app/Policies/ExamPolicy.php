<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Exam;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;

class ExamPolicy
{
    public function view(User $user, Exam $exam): bool
    {
        if ($this->isStaff($user) || $this->ownsExam($user, $exam)) {
            return true;
        }

        if ($user->hasRole(RoleEnum::Student->value)) {
            return StudentProfile::where('user_id', $user->id)
                ->where('classroom_id', $exam->classroom_id)
                ->exists();
        }

        return false;
    }

    // الإجابات الصح: الإدارة وصاحب الاختبار بس
    public function viewAnswers(User $user, Exam $exam): bool
    {
        return $this->isStaff($user) || $this->ownsExam($user, $exam);
    }

    public function update(User $user, Exam $exam): bool
    {
        return $this->ownsExam($user, $exam);
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $this->ownsExam($user, $exam);
    }

    private function isStaff(User $user): bool
    {
        return $user->hasAnyRole([RoleEnum::Admin->value, RoleEnum::Manager->value]);
    }

    private function ownsExam(User $user, Exam $exam): bool
    {
        if (! $user->hasRole(RoleEnum::Teacher->value)) {
            return false;
        }

        return TeacherProfile::where('user_id', $user->id)->value('id') === $exam->teacher_profile_id;
    }
}
