<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Services\SubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\traits\ApiResponse;

class SubjectController extends Controller
{
    public function __construct(private SubjectService $subjects) {}
use ApiResponse;
    public function index(): JsonResponse
    {
        return $this->paginated(SubjectResource::collection($this->subjects->list()));
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $subject = $this->subjects->create($request->validated());

        return $this->created(new SubjectResource($subject), 'Subject created');
    }

    public function show(Subject $subject): JsonResponse
    {
        return $this->success(new SubjectResource($subject));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): JsonResponse
    {
        $subject = $this->subjects->update($subject, $request->validated());

        return $this->success(new SubjectResource($subject), 'Subject updated');
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $this->subjects->delete($subject);

        return $this->success(null, 'Subject deleted');
    }
}
