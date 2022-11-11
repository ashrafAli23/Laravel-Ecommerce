<?php

declare(strict_types=1);

namespace App\Repository;

use App\Http\Interface\Repository\IAccountRepository;
use Illuminate\Database\Eloquent\Model;

class AccountRepository implements IAccountRepository
{
    public function updateUserProfile(int $id): Model
    {
    }

    public function showUserProfile(int $id): Model
    {
    }
}
