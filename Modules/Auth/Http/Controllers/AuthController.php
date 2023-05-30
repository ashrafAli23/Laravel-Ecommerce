<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Dto\LoginDto;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Services\AuthService;
use Modules\Common\Http\Response\BaseResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }
    public function login(LoginRequest $request, BaseResponse $baseResponse)
    {
        try {
            $token = $this->authService->login(LoginDto::create($request));
            return $baseResponse
                ->setMessage("Login success")
                ->setData(["token" => $token])
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage($th->getMessage())
                ->setSuccess(false)
                ->toApiResponse();
        }
    }


    public function logout(Request $request, BaseResponse $baseResponse)
    {
        try {
            $request->user()->tokens()->delete();
            return $baseResponse->setMessage("Logout successfull")->toApiResponse();
        } catch (\Throwable $th) {
            return $baseResponse
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage("Failed to logout")
                ->toApiResponse();
        }
    }
}
