<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleEntryResource;
use App\Models\ScheduleEntry;
use App\Http\Requests\Schedule\StoreScheduleEntryRequest;
use App\Http\Requests\Schedule\UpdateScheduleEntryRequest;
use App\Services\ScheduleEntryService;
use Illuminate\Http\JsonResponse;

class ScheduleEntryController extends Controller
{
    public function __construct(private ScheduleEntryService $entries) {}

    public function index(): JsonResponse
    {
        return $this->paginated(ScheduleEntryResource::collection($this->entries->list()));
    }

    public function store(StoreScheduleEntryRequest $request): JsonResponse
    {
        $entry = $this->entries->create($request->validated());

        return $this->created(new ScheduleEntryResource($entry), 'Schedule entry created');
    }

    public function show(ScheduleEntry $scheduleEntry): JsonResponse
    {
        return $this->success(new ScheduleEntryResource(
            $scheduleEntry->load('classroom.level', 'subject', 'teacher.user')
        ));
    }

    public function update(UpdateScheduleEntryRequest $request, ScheduleEntry $scheduleEntry): JsonResponse
    {
        $entry = $this->entries->update($scheduleEntry, $request->validated());

        return $this->success(new ScheduleEntryResource($entry), 'Schedule entry updated');
    }

    public function destroy(ScheduleEntry $scheduleEntry): JsonResponse
    {
        $this->entries->delete($scheduleEntry);

        return $this->success(null, 'Schedule entry deleted');
    }
}
