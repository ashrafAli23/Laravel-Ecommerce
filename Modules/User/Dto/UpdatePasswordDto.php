<?php

declare(strict_types=1);

namespace Modules\User\Dto;

use Modules\User\Http\Requests\V1\UpdatePasswordRequest;


class UpdatePasswordDto
{
    public readonly string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * @param UpdatePasswordRequest $request
     * @return self
     */
    public static function create(UpdatePasswordRequest $request): self
    {
        return new self($request->password);
    }
}
