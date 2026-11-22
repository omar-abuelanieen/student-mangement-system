<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\GuardianProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamSubmissionPolicy
{
    public function view(User $user, ExamSubmission $submission): bool
    {
        if ($user->hasAnyRole([RoleEnum::Admin->value, RoleEnum::Manager->value])) {
            return true;
        }

        if ($user->hasRole(RoleEnum::Student->value)) {
            return StudentProfile::where('user_id', $user->id)
                ->whereKey($submission->student_profile_id)
                ->exists();
        }

        if ($user->hasRole(RoleEnum::Guardian->value)) {
            $guardianId = GuardianProfile::where('user_id', $user->id)->value('id');

            return $guardianId && DB::table('guardian_student')
                ->where('guardian_profile_id', $guardianId)
                ->where('student_profile_id', $submission->student_profile_id)
                ->exists();
        }

        return $this->teachesExam($user, $submission);
    }

    public function grade(User $user, ExamSubmission $submission): bool
    {
        return $this->teachesExam($user, $submission);
    }

    private function teachesExam(User $user, ExamSubmission $submission): bool
    {
        if (! $user->hasRole(RoleEnum::Teacher->value)) {
            return false;
        }

        $teacherId = TeacherProfile::where('user_id', $user->id)->value('id');

        return $teacherId !== null
            && Exam::whereKey($submission->exam_id)->value('teacher_profile_id') === $teacherId;
    }
}
