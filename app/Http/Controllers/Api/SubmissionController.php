<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\GradeSubmissionRequest;
use App\Http\Requests\Submission\SubmitExamRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    public function __construct(private SubmissionService $submissions) {}

    public function index(Exam $exam): JsonResponse
    {
        Gate::authorize('viewAnswers', $exam);

        return $this->paginated(SubmissionResource::collection($this->submissions->listForExam($exam)));
    }

    public function store(SubmitExamRequest $request, Exam $exam): JsonResponse
    {
        $submission = $this->submissions->submit($request->user(), $exam, $request->validated('answers'));

        return $this->created(new SubmissionResource($submission), 'Exam submitted');
    }

    public function show(ExamSubmission $submission): JsonResponse
    {
        Gate::authorize('view', $submission);

        $submission = $this->submissions->loadDetails($submission);

        return $this->success(
            (new SubmissionResource($submission))->revealAnswers(Gate::allows('viewAnswers', $submission->exam))
        );
    }

    public function grade(GradeSubmissionRequest $request, ExamSubmission $submission): JsonResponse
    {
        $submission = $this->submissions->grade($request->user(), $submission, $request->validated('answers'));

        return $this->success(
            (new SubmissionResource($submission))->revealAnswers(),
            'Submission graded'
        );
    }
}
