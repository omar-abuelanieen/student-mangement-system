<?php

namespace App\Services;

use App\Models\GuardianProfile;
use Illuminate\Pagination\LengthAwarePaginator;

class GuardianService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return GuardianProfile::with('user', 'students.user')
            ->latest('id')
            ->paginate($perPage);
    }

    public function load(GuardianProfile $guardian): GuardianProfile
    {
        return $guardian->load('user', 'students.user');
    }

    public function syncStudents(GuardianProfile $guardian, array $students): GuardianProfile
    {
        $payload = collect($students)->mapWithKeys(fn (array $student) => [
            $student['student_profile_id'] => ['relationship' => $student['relationship'] ?? null],
        ])->all();

        $guardian->students()->sync($payload);

        return $this->load($guardian);
    }
}
