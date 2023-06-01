<?php

declare(strict_types=1);

namespace Modules\Products\Service;

use Illuminate\Http\Request;
use Modules\Common\Enums\BaseStatusEnum;
use Modules\Products\Dto\CreateBrandDto;
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
        $condition = [];
        $orderBy = "desc";
        if (isset($request->search)) {
            $condition[] = ['name', 'LIKE', "%$request->search%"];
        }

        if (isset($request->order)) {
            $orderBy = $request->orderBy;
        }

        $brand = $this->brandRepository->advancedGet([
            'with' => [],
            'condition' => $condition,
            'order_by' => ['created_at' => $orderBy],
            'paginate' => [
                'per_page' => (int)$request->perPage,
                'current_paged' => (int)$request->page,
            ],
        ]);

        return $brand;
    }

    public function create(CreateBrandDto $createBrandDto)
    {
        $brand = $this->brandRepository->createOrUpdate([
            'name' => $createBrandDto->name,
            'description' => $createBrandDto->description,
            'order' => $createBrandDto->order,
            'is_feature' => $createBrandDto->is_feature,
            'status' => $createBrandDto->status,
        ]);

        return $brand;
    }

    public function destroy(int $id)
    {
        $brand = $this->brandRepository->findOrFail($id);
        $this->brandRepository->delete($brand);
    }
}
