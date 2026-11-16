<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Level\StoreLevelRequest;
use App\Http\Requests\Level\UpdateLevelRequest;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use App\Services\LevelService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LevelController extends Controller
{
    use ApiResponse;

    public function __construct(private LevelService $levels) {}

    public function index(): JsonResponse
    {
        return $this->paginated(LevelResource::collection($this->levels->list()));
    }

    public function store(StoreLevelRequest $request): JsonResponse
    {
        $level = $this->levels->create($request->validated());
        return $this->created(new LevelResource($level), 'Level created');
    }

    public function show(Level $level): JsonResponse
    {
        return $this->success(new LevelResource($level));
    }

    public function update(UpdateLevelRequest $request, Level $level): JsonResponse
    {
        $level = $this->levels->update($level, $request->validated());
        return $this->success(new LevelResource($level), 'Level updated');
    }

    public function destroy(Level $level): JsonResponse
    {
        $this->levels->delete($level);
        return $this->success(null, 'Level deleted');
    }
}
