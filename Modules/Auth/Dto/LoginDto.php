<?php

declare(strict_types=1);

namespace Modules\Auth\Dto;

use Modules\Auth\Http\Requests\LoginRequest;

class LoginDto
{
    public readonly string $email;
    public readonly string $password;


    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @param string $email
     * @param string $password
     * @return self
     */
    public static function create(LoginRequest $request): self
    {
        return new self($request->email, $request->password);
    }
}
