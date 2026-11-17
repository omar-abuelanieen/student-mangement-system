<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleEntryResource;
use App\Models\TeacherProfile;
use App\Services\ScheduleEntryService;
use Illuminate\Http\JsonResponse;

class TeacherScheduleController extends Controller
{
    public function __construct(private ScheduleEntryService $entries) {}

    public function __invoke(TeacherProfile $teacher): JsonResponse
    {
        $schedule = collect($this->entries->teacherSchedule($teacher))
            ->map(fn ($entries) => ScheduleEntryResource::collection($entries))
            ->all();

        return $this->success($schedule);
    }
}
