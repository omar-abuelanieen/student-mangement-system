<?php

namespace App\Services;

use App\Models\Level;
use Illuminate\Pagination\LengthAwarePaginator;

class LevelService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Level::orderBy('grade')->paginate($perPage);
    }

    public function create(array $data): Level
    {
        return Level::create($data);
    }

    public function update(Level $level, array $data): Level
    {
        $level->update($data);
        return $level->refresh();
    }

    public function delete(Level $level): void
    {
        $level->delete();
    }
}
