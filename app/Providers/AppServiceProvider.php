<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interfaces\IUser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IUser::class, function () {
            return new UserRepository(new User());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}