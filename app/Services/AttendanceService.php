<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\TeacherProfile;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function markBulk(int $userId, int $classroomId, string $date, array $records): Collection
    {
        $teacherId = TeacherProfile::where('user_id', $userId)->value('id');
        $now = now();

        $rows = collect($records)->map(fn (array $record) => [
            'student_profile_id' => $record['student_profile_id'],
            'classroom_id'       => $classroomId,
            'marked_by'          => $teacherId,
            'date'               => $date,
            'status'             => $record['status'],
            'marked_at'          => $now->format('Y-m-d H:i:s.v'),
            'created_at'         => $now->toDateTimeString(),
            'updated_at'         => $now->toDateTimeString(),
        ]);

        try {
            Attendance::insert($rows->all());
        } catch (UniqueConstraintViolationException) {
            // خط الدفاع الأخير: طلبين وصلوا في نفس اللحظة
            throw ValidationException::withMessages([
                'records' => ['Attendance was already recorded for one or more of these students on this date.'],
            ]);
        }

        return Attendance::with('student.user')
            ->where('date', $date)
            ->whereIn('student_profile_id', $rows->pluck('student_profile_id'))
            ->get();
    }

    public function update(Attendance $attendance, int $userId, string $status): Attendance
    {
        $teacherId = TeacherProfile::where('user_id', $userId)->value('id');
        $now = now();

        Attendance::whereKey($attendance->id)->update([
            'status'     => $status,
            'marked_by'  => $teacherId,
            'marked_at'  => $now->format('Y-m-d H:i:s.v'),
            'updated_at' => $now,
        ]);

        return $attendance->refresh()->load('student.user');
    }
}
