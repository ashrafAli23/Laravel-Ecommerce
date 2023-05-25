<?php

declare(strict_types=1);

namespace Modules\User\Service;

use Exception;
use Modules\Common\Dto\SelectedList;
use Modules\User\Dto\CreateRoleDto;
use Modules\User\Repositories\Interfaces\IRoleRepository;
use Symfony\Component\HttpFoundation\Response;

class RoleService
{
    private IRoleRepository $roleRepository;
    public function __construct(IRoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
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
    }

    public function update(int $id)
    {
        # code...
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
}
