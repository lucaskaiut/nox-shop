<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Domain\Models\User;
use App\Modules\User\Domain\Services\UserService;
use App\Modules\User\Http\Requests\UserCreateResetPasswordRequest;
use App\Modules\User\Http\Requests\UserLoginRequest;
use App\Modules\User\Http\Requests\UserResetPasswordRequest;
use App\Modules\User\Http\Requests\UserStoreRequest;
use App\Modules\User\Http\Requests\UserUpdateRequest;
use App\Modules\User\Http\Resources\UserCollection;
use App\Modules\User\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private UserService $service;
    
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        return new UserCollection($this->service->paginate($request->all()));
    }

    public function store(UserStoreRequest $request)
    {
        $this->authorize('create', User::class);

        return DB::transaction(function () use ($request) {
            return (new UserResource($this->service->create($request->validated())))->response()->setStatusCode(201);
        });
    }

    public function show(int $id)
    {
        $user = $this->service->findOrFail($id);

        $this->authorize('show', $user);

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = $this->service->findOrFail($id);

            $this->authorize('update', $user);

            return new UserResource($this->service->update($user, $request->validated()));
        });
    }

    public function destroy(int $id)
    {
        $user = $this->service->findOrFail($id);

        $this->authorize('delete', $user);

        $this->service->delete($user);

        return response()->json([], 204);
    }

    public function login(UserLoginRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return (new UserResource($this->service->login($request->validated())));
        });
    }

    public function sendResetPasswordLink(UserCreateResetPasswordRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->service->createPasswordReset(collect($request->validated())->get('email'));
        });
    }

    public function resetPassword(UserResetPasswordRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->service->resetPassword($request->validated());
        });
    }
}
