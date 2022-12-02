<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Interface\Repository\IRepository;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Chatting;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\Units;
use App\Repository\Repository;
use App\Services\BannerService;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ChattingService;
use App\Services\ProductRatingService;
use App\Services\ProductService;
use App\Services\UnitsService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->serviceContainer();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    private function serviceContainer(): void
    {
        $this->app->when(CategoryService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new Category);
            });

        $this->app->when(BrandService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new Brand);
            });

        $this->app->when(BannerService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new Banner);
            });

        $this->app->when(ProductService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new Product);
            });

        $this->app->when(ChattingService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new Chatting);
            });

        $this->app->when(UnitsService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new Units);
            });

        $this->app->when(ProductRatingService::class)->needs(IRepository::class)
            ->give(function () {
                return new Repository(new ProductRating);
            });
    }
}
