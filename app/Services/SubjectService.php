<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Pagination\LengthAwarePaginator;

class SubjectService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Subject::orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Subject
    {
        return Subject::create($data);
    }

    public function update(Subject $subject, array $data): Subject
    {
        $subject->update($data);

        return $subject->refresh();
    }

    public function delete(Subject $subject): void
    {
        $subject->delete();
    }
}
