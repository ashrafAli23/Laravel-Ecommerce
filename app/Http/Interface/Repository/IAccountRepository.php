<?php

declare(strict_types=1);

namespace App\Http\Interface\Repository;

use Illuminate\Database\Eloquent\Model;

interface IAccountRepository
{
    public function showUserProfile(int $id): Model;
    public function updateUserProfile(int $id): Model;
}
