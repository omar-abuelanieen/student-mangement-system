<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\TeacherProfile;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Teacher\SyncSubjectsRequest;

class TeacherController extends Controller
{
    public function __construct(private TeacherService $teachers) {}

    public function index(): JsonResponse
    {
        return $this->paginated(TeacherResource::collection($this->teachers->list()));
    }

    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $teacher = $this->teachers->create($request->validated());

        return $this->created(new TeacherResource($teacher), 'Teacher created');
    }

    public function show(TeacherProfile $teacher): JsonResponse
    {
        return $this->success(new TeacherResource($teacher->load('user', 'classroom', 'subjects')));
    }

    public function update(UpdateTeacherRequest $request, TeacherProfile $teacher): JsonResponse
    {
        $teacher = $this->teachers->update($teacher, $request->validated());

        return $this->success(new TeacherResource($teacher), 'Teacher updated');
    }

    public function destroy(TeacherProfile $teacher): JsonResponse
    {
        $this->teachers->delete($teacher);

        return $this->success(null, 'Teacher deleted');
    }
    public function syncSubjects(SyncSubjectsRequest $request, TeacherProfile $teacher): JsonResponse
{
    $teacher = $this->teachers->syncSubjects($teacher, $request->validated('subject_ids'));

    return $this->success(new TeacherResource($teacher), 'Teacher subjects updated');
}
}
