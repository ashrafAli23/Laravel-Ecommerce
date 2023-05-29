<?php

declare(strict_types=1);

namespace Modules\User\Facades;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "UserService";
    }
}
