<?php

declare(strict_types=1);

namespace Modules\User\Service;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Dto\SelectedList;
use Modules\Common\Http\Response\BaseResponse;
use Modules\User\Dto\CreateUserDto;
use Modules\User\Repositories\Interfaces\IRoleRepository;
use Modules\User\Repositories\Interfaces\IUserRepository;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    public function __construct(
        private IUserRepository $userRepository,
        private IRoleRepository $roleRepository
    ) {
    }

    public function findAll(Request $request)
    {
        $perPage = 25;
        if (isset($request->perPage)) {
            $perPage = $request->perPage > 100 ? 100 : $request->perPage;
        }

        $query = $this->userRepository
            ->getModel()
            ->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'user_roles.role_id');

        if (isset($request->username) || isset($request->search)) {
            $query->where('users.username', '=', $request->username)
                ->where('username', 'LIKE' . "%$request->search%");
        }

        $user =  $query->select([
            'users.id as id',
            'username',
            'email',
            'roles.name as role_name',
            'roles.id as role_id',
            'users.updated_at as updated_at',
            'users.created_at as created_at',
            'super_user',
        ])->paginate($perPage);

        if (empty($user) || !isset($user)) {
            throw new Exception("Empty data", Response::HTTP_NO_CONTENT);
        }

        return $user;
    }

    public function create(CreateUserDto $createUserDto)
    {
        $user = $this->userRepository->createOrUpdate([
            'first_name' => $createUserDto->first_name,
            'last_name' => $createUserDto->last_name,
            'username' => $createUserDto->username,
            'email' => $createUserDto->email,
            'password' => Hash::make($createUserDto->password)
        ]);
        if (isset($createUserDto->roleId)) {
            $role = $this->roleRepository->findById($createUserDto->roleId);
            if (!empty($role)) {
                $role->users()->attach($user->id);
            }
        }
        return $user;
    }

    public function findOne(Request $request, int $id)
    {
        if ($request->user()->isSuperUser() || $request->user()->id == $id) {
            $user = $this->userRepository->findById($id);
            return $user;
        }

        throw new Exception("User not found", 404);
    }

    public function update(Request $request)
    {
    }

    public function delete(Request $request, int $id)
    {
        $user = $this->userRepository->findOrFail($id);

        if (!$request->user()->isSuperUser() && $user->isSuperUser()) {
            throw new Exception("Can not delete super admin", Response::HTTP_BAD_REQUEST);
        }
        $this->userRepository->delete($user);
    }

    public function deletes(Request $request, SelectedList $list)
    {
        $listId = $list->listIds;
        if (empty($listIds)) {
            throw new Exception("Please select at least one record to perform this action!", Response::HTTP_BAD_REQUEST);
        }

        foreach ($listId as $id) {
            if ($request->user()->id == $id) {
                throw new Exception("Can not delete logged in user", Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userRepository->findOrFail($id);
            if (!$request->user()->isSuperUser() && $user->isSuperUser()) {
                continue;
            }
            $this->userRepository->delete($user);
        }
    }
}