<?php

declare(strict_types=1);

namespace Modules\User\Dto;

use Modules\User\Http\Requests\CreateUserRequest;

class CreateUserDto
{
    public readonly string $first_name;
    public readonly string $last_name;
    public readonly string $username;
    public readonly string $email;
    public readonly string $password;
    public readonly int|null $roleId;

    public function __construct(
        string $first_name,
        string $last_name,
        string $username,
        string $email,
        string $password,
        int|null $roleId,
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->roleId = $roleId;
    }

    /**
     * @param CreateUserRequest $request
     * @return self
     */
    public static function create(CreateUserRequest $request): self
    {
        return new self(
            $request->first_name,
            $request->last_name,
            $request->username,
            $request->email,
            $request->password,
            $request->roleId ?? null
        );
    }
}