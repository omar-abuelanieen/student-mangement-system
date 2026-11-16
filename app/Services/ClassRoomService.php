<?php

namespace App\Services;

use App\Models\Classroom;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassroomService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Classroom::with('level')
            ->orderBy('level_id')
            ->orderBy('section')
            ->paginate($perPage);
    }

    public function create(array $data): Classroom
    {
        return Classroom::create($data)->load('level');
    }

    public function update(Classroom $classroom, array $data): Classroom
    {
        $classroom->update($data);

        return $classroom->refresh()->load('level');
    }

    public function delete(Classroom $classroom): void
    {
        $classroom->delete();
    }
}
