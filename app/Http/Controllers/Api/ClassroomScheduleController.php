<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleEntryResource;
use App\Models\Classroom;
use App\Services\ScheduleEntryService;
use Illuminate\Http\JsonResponse;

class ClassroomScheduleController extends Controller
{
    public function __construct(private ScheduleEntryService $entries) {}

    public function __invoke(Classroom $classroom): JsonResponse
    {
        $schedule = collect($this->entries->classroomSchedule($classroom))
            ->map(fn ($entries) => ScheduleEntryResource::collection($entries))
            ->all();

        return $this->success($schedule);
    }
}
