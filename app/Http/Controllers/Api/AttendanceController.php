<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendance) {}

public function index(): JsonResponse
    {
        return $this->paginated(AttendanceResource::collection(Attendance::query()->paginate()));
    }



    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $records = $this->attendance->markBulk(
            $request->user()->id,
            $data['classroom_id'],
            $data['date'],
            $data['records'],
        );

        return $this->created(AttendanceResource::collection($records), 'Attendance recorded');
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance): JsonResponse
    {
        $attendance = $this->attendance->update(
            $attendance,
            $request->user()->id,
            $request->validated('status'),
        );

        return $this->success(new AttendanceResource($attendance), 'Attendance updated');
    }
}
