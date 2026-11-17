<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\TeacherProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeacherService
{
    public function __construct(private UserService $users) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return TeacherProfile::with('user', 'classroom', 'subjects')
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(array $data): TeacherProfile
    {
        $user = $this->users->create($data, RoleEnum::Teacher);

        return $user->teacherProfile->load('user',);
    }

    public function update(TeacherProfile $teacher, array $data): TeacherProfile
    {
        $teacher->loadMissing('user');
        $teacher->user->update($data);

        return $teacher->refresh()->load('user');
    }

    public function delete(TeacherProfile $teacher): void
    {
        DB::transaction(function () use ($teacher) {
            $teacher->loadMissing('user');
            $teacher->user->delete();
            $teacher->delete();
        });
    }
    public function syncSubjects(TeacherProfile $teacher, array $subjectIds): TeacherProfile
{
    $teacher->subjects()->sync($subjectIds);

    return $teacher->load('user', 'classroom', 'subjects');
}
}
