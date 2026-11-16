<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Classroom\StoreClassroomRequest;
use App\Http\Requests\Classroom\UpdateClassroomRequest;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use App\Services\ClassroomService;
use Illuminate\Http\JsonResponse;

class ClassroomController extends Controller
{
    public function __construct(private ClassroomService $classrooms) {}

    public function index(): JsonResponse
    {
        return $this->paginated(ClassroomResource::collection($this->classrooms->list()));
    }

    public function store(StoreClassroomRequest $request): JsonResponse
    {
        $classroom = $this->classrooms->create($request->validated());

        return $this->created(new ClassroomResource($classroom), 'Classroom created');
    }

    public function show(Classroom $classroom): JsonResponse
    {
        return $this->success(new ClassroomResource($classroom->load('level')));
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): JsonResponse
    {
        $classroom = $this->classrooms->update($classroom, $request->validated());

        return $this->success(new ClassroomResource($classroom), 'Classroom updated');
    }

    public function destroy(Classroom $classroom): JsonResponse
    {
        $this->classrooms->delete($classroom);

        return $this->success(null, 'Classroom deleted');
    }
}
