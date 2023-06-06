<?php

declare(strict_types=1);

namespace Modules\Products\Service;

use Modules\Common\Dto\SelectedList;
use Modules\Products\Dto\Category\CreateCategoryDto;
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

    // public function findAll(Request $request)
    // {
    //     $condition = [];
    //     $orderBy = [];

    //     if (isset($request->search)) {
    //         $condition[] = ['name', 'LIKE', "%$request->search%"];
    //     }

    //     if (isset($request->order_by)) {
    //         $orderBy = ['created_at' => $request->order_by];
    //     }

    //     $brand = $this->brandRepository->advancedGet([
    //         'with' => [],
    //         'condition' => $condition,
    //         'order_by' => $orderBy,
    //         'paginate' => [
    //             'per_page' => (int)$request->per_page,
    //             'current_paged' => (int)$request->page,
    //         ],
    //     ]);

    //     return $brand;
    // }

    public function findOne(int $id)
    {
        return $this->categoryRepository->findOrFail($id);
    }

    public function create(CreateCategoryDto $categoryDto)
    {
        $category = $this->categoryRepository->createOrUpdate([
            'name' => $categoryDto->name,
            'slug' => $categoryDto->slug,
            'description' => $categoryDto->description,
            'is_featured' => $categoryDto->is_featured,
            'order' => $categoryDto->order,
            'meta' => json_encode($categoryDto->meta),
            'status' => $categoryDto->status,
            'parent_id' => $categoryDto->parent_id
        ]);

        return $category;
    }

    public function update(int $id, CreateCategoryDto $categoryDto)
    {
        $category = $this->categoryRepository->findOrFail($id);
        $category->fill([
            'id' => $id,
            'name' => $categoryDto->name,
            'slug' => $categoryDto->slug,
            'description' => $categoryDto->description,
            'is_featured' => $categoryDto->is_featured,
            'order' => $categoryDto->order,
            'meta' => json_encode($categoryDto->meta),
            'status' => $categoryDto->status,
            'parent_id' => $categoryDto->parent_id
        ]);

        return $this->categoryRepository->createOrUpdate($category);
    }


    public function destroy(int $id)
    {
        $category = $this->categoryRepository->findOrFail($id);
        $this->categoryRepository->delete($category);
    }

    public function deletes(SelectedList $list)
    {
        $listId = $list->listIds;
        foreach ($listId as $id) {
            $category = $this->categoryRepository->findOrFail($id);
            $this->categoryRepository->delete($category);
        }
    }
}
