<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\StoreExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExamController extends Controller
{
    public function __construct(private ExamService $exams) {}

    public function index(Request $request): JsonResponse
    {
        return $this->paginated(ExamResource::collection($this->exams->list($request->user())));
    }

    public function store(StoreExamRequest $request): JsonResponse
    {
        $exam = $this->exams->create($request->user()->id, $request->validated());

        return $this->created((new ExamResource($exam))->revealAnswers(), 'Exam created');
    }

    public function show(Exam $exam): JsonResponse
    {
        Gate::authorize('view', $exam);

        $exam = $this->exams->loadDetails($exam);

        return $this->success(
            (new ExamResource($exam))->revealAnswers(Gate::allows('viewAnswers', $exam))
        );
    }

    public function update(UpdateExamRequest $request, Exam $exam): JsonResponse
    {
        $exam = $this->exams->updateTitle($exam, $request->validated('title'));

        return $this->success(new ExamResource($exam), 'Exam updated');
    }

    public function destroy(Exam $exam): JsonResponse
    {
        Gate::authorize('delete', $exam);

        $this->exams->delete($exam);

        return $this->success(null, 'Exam deleted');
    }
}
