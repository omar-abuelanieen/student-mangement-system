<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $users) {}

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->users->create($data, RoleEnum::from($data['role']));

        return $this->created(new UserResource($user), 'User created');
    }
}
