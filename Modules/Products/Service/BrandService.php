<?php

declare(strict_types=1);

namespace Modules\Products\Service;

use Illuminate\Http\Request;
use Modules\Common\Dto\SelectedList;
use Modules\Products\Dto\CreateBrandDto;
use Modules\Products\Repositories\Interfaces\IBrandRepository;

class BrandService
{
    /**
     * @param IBrandRepository $brandRepository
     */
    public function __construct(
        private readonly IBrandRepository $brandRepository
    ) {
    }

    public function findAll(Request $request)
    {
        $condition = [];
        $orderBy = [];

        if (isset($request->search)) {
            $condition[] = ['name', 'LIKE', "%$request->search%"];
        }

        if (isset($request->order_by)) {
            $orderBy = ['created_at' => $request->order_by];
        }

        $brand = $this->brandRepository->advancedGet([
            'with' => [],
            'condition' => $condition,
            'order_by' => $orderBy,
            'paginate' => [
                'per_page' => (int)$request->per_page,
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

    public function findOne(int $id)
    {
        return $this->brandRepository->findOrFail($id);
    }

    public function update(CreateBrandDto $createBrandDto, int $id)
    {
        $brand = $this->brandRepository->findOrFail($id);
        $brand->fill([
            'id' => $id,
            'name' => $createBrandDto->name,
            'description' => $createBrandDto->description,
            'order' => $createBrandDto->order,
            'is_feature' => $createBrandDto->is_feature,
            'status' => $createBrandDto->status,
        ]);

        return $this->brandRepository->createOrUpdate($brand);
    }

    public function destroy(int $id)
    {
        $brand = $this->brandRepository->findOrFail($id);
        $this->brandRepository->delete($brand);
    }

    public function deletes(SelectedList $list)
    {
        $listId = $list->listIds;
        foreach ($listId as $id) {
            $brand = $this->brandRepository->findOrFail($id);
            $this->brandRepository->delete($brand);
        }
    }
}