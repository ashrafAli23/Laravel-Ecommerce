<?php

declare(strict_types=1);

namespace Modules\Auth\Services;

use Exception;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Dto\LoginDto;
use Modules\User\Repositories\Interfaces\IUserRepository;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function __construct(private readonly IUserRepository $userRepository)
    {
    }
    public function login(LoginDto $loginDto)
    {
        $user = $this->userRepository->getFirstBy(['email' => $loginDto->email]);
        if (!isset($user) || !Hash::check($loginDto->password, $user->password)) {
            throw new Exception("Invalid email or password", Response::HTTP_BAD_REQUEST);
        }
        return $user->createToken("api-token")->plainTextToken;
    }
}
