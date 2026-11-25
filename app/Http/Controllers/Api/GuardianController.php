<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guardian\SyncGuardianStudentsRequest;
use App\Http\Resources\GuardianResource;
use App\Models\GuardianProfile;
use App\Services\GuardianService;
use Illuminate\Http\JsonResponse;

class GuardianController extends Controller
{
    public function __construct(private GuardianService $guardians) {}

    public function index(): JsonResponse
    {
        return $this->paginated(GuardianResource::collection($this->guardians->list()));
    }

    public function show(GuardianProfile $guardian): JsonResponse
    {
        return $this->success(new GuardianResource($this->guardians->load($guardian)));
    }

    public function syncStudents(SyncGuardianStudentsRequest $request, GuardianProfile $guardian): JsonResponse
    {
        $guardian = $this->guardians->syncStudents($guardian, $request->validated('students'));

        return $this->success(new GuardianResource($guardian), 'Guardian students updated');
    }
}
