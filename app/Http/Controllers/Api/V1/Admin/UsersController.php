<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Requests\V1\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repository\Repository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as STATUS;

class UsersController extends Controller
{
    use Response;
    private Repository $user;
    public function __construct(User $user)
    {
        $this->user = new Repository($user);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginateRequest $request): JsonResponse
    {

        try {
            $entries = $request->entries ?? 10;


            $data = $this->user
                ->index()->query()->paginate($entries);

            return $this
                ->dataResponse(UserResource::collection($data), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $request->validate([
                'password' => 'required|min:6'
            ]);

            $this->user->store([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return $this->successResponse("User created successfully", STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->user->show((int)$id);

            if (!$user) {
                return $this->errorResponse("User not found", STATUS::HTTP_NOT_FOUND);
            }

            return $this->dataResponse(new UserResource($user), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->user->update((int)$id);

            if (!$user) {
                return $this->errorResponse("User not found", STATUS::HTTP_NOT_FOUND);
            }

            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ];

            if (isset($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return $this->successResponse("User updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $data = $this->user->delete($id);
            if (!$data) {
                return $this->errorResponse("User not found", STATUS::HTTP_NOT_FOUND);
            }
            $data->delete();
            return $this->successResponse("User deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
