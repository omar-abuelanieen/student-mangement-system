<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\StudentProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StudentService
{
    public function __construct(private UserService $users) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return StudentProfile::with('user', 'classroom.level')
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(array $data): StudentProfile
    {
        $user = $this->users->create($data, RoleEnum::Student);

        return $user->studentProfile->load('user', 'classroom.level');
    }

    public function update(StudentProfile $student, array $data): StudentProfile
    {
        $student->user->update($data);

        return $student->refresh()->load('user', 'classroom.level');
    }

    public function delete(StudentProfile $student): void
    {
        DB::transaction(function () use ($student) {
            $student->user->delete();
            $student->delete();
        });
    }

    public function assignToClassroom(StudentProfile $student, int $classroomId): StudentProfile
    {
        $student->update(['classroom_id' => $classroomId]);

        return $student->load('user', 'classroom.level');
    }
}
