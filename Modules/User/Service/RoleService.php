<?php

declare(strict_types=1);

namespace Modules\User\Service;

use Exception;
use Modules\Common\Dto\SelectedList;
use Modules\User\Dto\AssignRoleDto;
use Modules\User\Dto\CreateRoleDto;
use Modules\User\Repositories\Interfaces\IRoleRepository;
use Modules\User\Repositories\Interfaces\IUserRepository;
use Symfony\Component\HttpFoundation\Response;

class RoleService
{
    private IRoleRepository $roleRepository;
    private IUserRepository $userRepository;

    public function __construct(IRoleRepository $roleRepository, IUserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $roles = $this->roleRepository->all();
        if (empty($roles)) {
            throw new Exception("Empty data", Response::HTTP_BAD_REQUEST);
        }
        return $roles;
    }

    public function create(CreateRoleDto $createRoleDto)
    {
        if ($createRoleDto->is_default) {
            $this->roleRepository->getModel()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $roles = $this->roleRepository->createOrUpdate([
            'name' => $createRoleDto->name,
            'slug' => $createRoleDto->slug,
            'permissions' => $this->cleanPermission($createRoleDto->permissions),
            'description' => $createRoleDto->description,
            'is_default' => $createRoleDto->is_default,
        ]);

        return $roles;
    }

    public function update(CreateRoleDto $createRoleDto, int $id)
    {
        if ($createRoleDto->is_default) {
            $this->roleRepository->getModel()->where('id', '!=', $id)->update(['is_default' => 0]);
        }
        $role = $this->roleRepository->findOrFail($id);
        $role->name = $createRoleDto->name;
        $role->slug = $createRoleDto->slug;
        $role->permissions = $this->cleanPermission($createRoleDto->permissions);
        $role->description = $createRoleDto->description;
        $role->is_default = $createRoleDto->is_default;
        $this->roleRepository->createOrUpdate($role);
    }

    public function delete(int $id)
    {
        $role = $this->roleRepository->findOrFail($id);

        $this->roleRepository->delete($role);
    }

    public function deletes(SelectedList $list)
    {
        $ids = $list->listIds;

        if (empty($ids)) {
            throw new Exception("Please select at least one record to perform this action!", Response::HTTP_BAD_REQUEST);
        }

        foreach ($ids as $id) {
            $role = $this->roleRepository->findOrFail($id);
            $this->delete($role);
        }
    }

    /**
     * Return a correct type cast permissions array
     * @param array $permissions
     * @return array
     */
    private function cleanPermission(array $permissions): array
    {
        if (!$permissions) {
            return [];
        }

        $cleanedPermissions = [];
        foreach ($permissions as $permissionName) {
            $cleanedPermissions[$permissionName] = true;
        }

        return $cleanedPermissions;
    }

    public function assignRole(AssignRoleDto $assignRoleDto)
    {
        $user = $this->userRepository->findOrFail($assignRoleDto->userId);
        $role = $this->roleRepository->findOrFail($assignRoleDto->roleId);

        $user->roles()->sync([$role->id]);
    }
}
