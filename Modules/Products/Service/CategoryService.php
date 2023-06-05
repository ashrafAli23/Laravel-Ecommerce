<?php

declare(strict_types=1);

namespace Modules\Products\Service;

use Modules\Products\Repositories\Interfaces\ICategoryRepository;

class CategoryService
{
    /**
     * @param ICategoryRepository $categoryRepository
     */
    public function __construct(
        private readonly ICategoryRepository $categoryRepository
    ) {
    }

    public function findAll()
    {
    }
}
