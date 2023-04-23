<?php

declare(strict_types=1);

use App\Models\User;
use App\Repositories\Interfaces\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private IUser $user;

    public function __construct(IUser $user)
    {
        $this->user = $user;
    }

    public function createUser(Request $request): User|bool
    {
        $data = array_push($request->all(), ['password' => Hash::make($request->password)]);
        $user = $this->user->createOrUpdate($data);

        return $user;
    }

    public function deleteUser($id)
    {
        $user = $this->user->findOrFail($id);

        if ($user->isSuper()) {
            throw new Exception("Cannot delete super user", 401);
        }

        $this->user->delete($user);
    }

    public function deleteUsers(Request $request)
    {
        $data = $request->ids;
        if (empty($data)) {
            throw new Exception("No selected users", 401);
        }

        foreach ($data as $id) {
            $this->deleteUser($id);
        }
    }
}
