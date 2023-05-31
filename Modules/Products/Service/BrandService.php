<?php

declare(strict_types=1);

namespace Modules\Products\Service;

use Illuminate\Http\Request;
use Modules\Products\Repositories\Interfaces\IBrandRepository;

class BrandService
{
    /**
     * @param IBrandRepository $brandRepository
     */
    public function __construct(private readonly IBrandRepository $brandRepository)
    {
    }

    public function findAll(Request $request)
    {
        if (isset($request->created_at)) {
        }

        $brand = $this->brandRepository->advancedGet([
            'with' => [],
            'condition' => [],
            'order_by' => ['created_at' => 'desc'],
            'paginate' => [
                'per_page' => (int)$request->per_page,
                'current_paged' => (int)$request->page,
            ],
        ]);
    }
}
