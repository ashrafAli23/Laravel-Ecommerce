<?php

declare(strict_types=1);

namespace Modules\User\Dto;

use Modules\User\Http\Requests\V1\UpdateUserRequest;

class UpdateUserDto
{
    public readonly string $first_name;
    public readonly string $last_name;
    public readonly string $username;
    public readonly string $email;

    public function __construct(
        string $first_name,
        string $last_name,
        string $username,
        string $email,
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->username = $username;
        $this->email = $email;
    }

    /**
     * @param UpdateUserRequest $request
     * @return self
     */
    public static function create(UpdateUserRequest $request): self
    {
        return new self(
            $request->first_name,
            $request->last_name,
            $request->username,
            $request->email,
        );
    }
}
