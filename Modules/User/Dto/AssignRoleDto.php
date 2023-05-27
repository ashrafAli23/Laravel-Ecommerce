<?php

declare(strict_types=1);

namespace Modules\User\Dto;

use Illuminate\Http\Request;

class AssignRoleDto
{
    public readonly int $userId;
    public readonly int $roleId;

    public function __construct(int $userId, int $roleId)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function create(Request $request): self
    {
        return new self($request->userId, $request->roleId);
    }
}
