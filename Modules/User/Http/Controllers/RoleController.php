<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Common\Dto\SelectedList;
use Modules\Common\Http\Response\BaseResponse;
use Modules\User\Dto\AssignRoleDto;
use Modules\User\Dto\CreateRoleDto;
use Modules\User\Http\Requests\CreateRoleRequest;
use Modules\User\Service\RoleService;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    public function __construct(private RoleService $roleService)
    {
    }

    public function index(BaseResponse $response): JsonResponse
    {
        try {
            $roles = $this->roleService->index();
            return $response->setData($roles)->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess()->setCode(Response::HTTP_FORBIDDEN)
                ->setMessage($th->getMessage())->toApiResponse();
        }
    }

    public function create(CreateRoleRequest $request, BaseResponse $baseResponse): JsonResponse
    {
        try {
            $roles = $this->roleService->create(CreateRoleDto::create($request));
            return $baseResponse
                ->setCode(Response::HTTP_CREATED)
                ->setData($roles)
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function update(CreateRoleRequest $request, int $id, BaseResponse $baseResponse): JsonResponse
    {
        try {
            $this->roleService->update(CreateRoleDto::create($request), $id);
            return $baseResponse->setCode(Response::HTTP_OK)
                ->setMessage("Role updated successfully")
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function destroy(int $id, BaseResponse $baseResponse): JsonResponse
    {
        try {
            $this->roleService->delete($id);
            return $baseResponse->setCode(Response::HTTP_OK)
                ->setMessage("Role deleted successfully")
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function deletes(Request $request, BaseResponse $baseResponse): JsonResponse
    {
        try {
            $this->roleService->deletes(SelectedList::create($request->ids));
            return $baseResponse->setMessage("List deletes successfully")->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse->setCode(Response::HTTP_BAD_REQUEST)
                ->setSuccess()
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function assignRole(Request $request, BaseResponse $baseResponse): JsonResponse
    {
        try {
            $this->roleService->assignRole(AssignRoleDto::create($request));

            return $baseResponse->setMessage("Update successfully")->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage($th->getMessage())
                ->setSuccess()->toApiResponse();
        }
    }
}
