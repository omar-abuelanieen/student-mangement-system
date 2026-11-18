<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\StoreCalendarDayRequest;
use App\Http\Requests\Calendar\UpdateCalendarDayRequest;
use App\Http\Resources\CalendarDayResource;
use App\Models\SchoolCalendarDay;
use App\Services\SchoolCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolCalendarDayController extends Controller
{
    public function __construct(private SchoolCalendarService $calendar) {}

    public function index(Request $request): JsonResponse
    {
        $year = $request->integer('year') ?: null;

        return $this->paginated(CalendarDayResource::collection($this->calendar->list($year)));
    }

    public function store(StoreCalendarDayRequest $request): JsonResponse
    {
        $day = $this->calendar->create($request->validated());

        return $this->created(new CalendarDayResource($day), 'Calendar day created');
    }

    public function show(SchoolCalendarDay $calendarDay): JsonResponse
    {
        return $this->success(new CalendarDayResource($calendarDay));
    }

    public function update(UpdateCalendarDayRequest $request, SchoolCalendarDay $calendarDay): JsonResponse
    {
        $day = $this->calendar->update($calendarDay, $request->validated());

        return $this->success(new CalendarDayResource($day), 'Calendar day updated');
    }

    public function destroy(SchoolCalendarDay $calendarDay): JsonResponse
    {
        $this->calendar->delete($calendarDay);

        return $this->success(null, 'Calendar day deleted');
    }
}
