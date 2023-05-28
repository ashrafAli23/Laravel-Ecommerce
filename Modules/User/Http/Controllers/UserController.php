<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\Common\Dto\SelectedList;
use Modules\Common\Http\Requests\SelectedListRequest;
use Modules\Common\Http\Response\BaseResponse;
use Modules\User\Dto\CreateUserDto;
use Modules\User\Http\Requests\CreateUserRequest;
use Modules\User\Service\UserService;
use Modules\User\Transformers\UserTransformer;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * @param Request $request
     * @param BaseResponse $baseResponse
     * @return JsonResponse|JsonResource
     */
    public function index(Request $request, BaseResponse $baseResponse): JsonResponse|JsonResource
    {
        try {
            $user = $this->userService->findAll($request);
            return $baseResponse
                ->setMessage("Success")
                ->setData(UserTransformer::collection($user))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess()
                ->setMessage($th->getMessage())
                ->setCode($th->getCode())
                ->toApiResponse();
        }
    }

    /**
     * @param CreateUserRequest $request
     * @param BaseResponse $baseResponse
     * @return JsonResponse|JsonResource
     */
    public function create(CreateUserRequest $request, BaseResponse $baseResponse): JsonResponse|JsonResource
    {
        try {
            $user = $this->userService->create(CreateUserDto::create($request));
            return $baseResponse
                ->setCode(Response::HTTP_CREATED)
                ->setMessage("Created successfully")
                ->setData(new UserTransformer($user))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param BaseResponse $baseResponse
     * @return JsonResponse|JsonResource
     */
    public function show(Request $request, int $id, BaseResponse $baseResponse): JsonResponse|JsonResource
    {
        try {
            $user = $this->userService->findOne($request, $id);
            return $baseResponse->setMessage("Success")
                ->setData(new UserTransformer($user))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setCode($th->getCode())
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function update(Request $request, int $id, BaseResponse $baseResponse): JsonResource|JsonResponse
    {
        try {
            // $this->userService->update();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess(false)
                ->setCode($th->getCode())
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function destroy(Request $request, int $id, BaseResponse $baseResponse): JsonResource|JsonResponse
    {
        try {
            $this->userService->destroy($request, $id);
            return $baseResponse
                ->setMessage("Deleted successfully")
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    public function deletes(SelectedListRequest $request, BaseResponse $baseResponse): JsonResource|JsonResponse
    {
        try {
            $this->userService->deletes($request, SelectedList::create($request));
            return $baseResponse->setData("Deletes successfully")->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->toApiResponse();
        }
    }
}
