<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\AssignClassroomRequest;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\StudentProfile;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    public function __construct(private StudentService $students) {}

    public function index(): JsonResponse
    {
        return $this->paginated(StudentResource::collection($this->students->list()));
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->students->create($request->validated());

        return $this->created(new StudentResource($student), 'Student created');
    }

    public function show(StudentProfile $student): JsonResponse
    {
        return $this->success(new StudentResource($student->load('user', 'classroom.level')));
    }

    public function update(UpdateStudentRequest $request, StudentProfile $student): JsonResponse
    {
        $student = $this->students->update($student, $request->validated());

        return $this->success(new StudentResource($student), 'Student updated');
    }

    public function destroy(StudentProfile $student): JsonResponse
    {
        $this->students->delete($student);

        return $this->success(null, 'Student deleted');
    }

    public function assignClassroom(AssignClassroomRequest $request, StudentProfile $student): JsonResponse
    {
        $student = $this->students->assignToClassroom($student, $request->validated('classroom_id'));

        return $this->success(new StudentResource($student), 'Student assigned to classroom');
    }
}
