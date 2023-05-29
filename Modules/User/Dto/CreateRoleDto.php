<?php

namespace Modules\User\Dto;

use Illuminate\Support\Str;
use Modules\User\Http\Requests\V1\CreateRoleRequest;

class CreateRoleDto
{
    public readonly string $name;
    public readonly string $slug;
    public readonly array $permissions;
    public readonly string $description;
    public readonly bool $is_default;

    public function __construct(string $name, string $description, bool $is_default, array $permissions)
    {
        $this->name = $name;
        $this->slug = Str::slug($name);
        $this->description = $description;
        $this->is_default = $is_default;
        $this->permissions = $permissions;
    }

    /**
     * @param string $name
     * @param string $description
     * @param boolean $is_default
     * @param array $permissions
     * @return self
     */
    public static function create(CreateRoleRequest $createRoleRequest): self
    {
        return new self($createRoleRequest->name, $createRoleRequest->description ?? "", $createRoleRequest->is_default ?? false, $createRoleRequest->permissions ?? []);
    }
}
