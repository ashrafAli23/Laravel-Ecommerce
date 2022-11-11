<?php

declare(strict_types=1);

namespace App\Repository;

use App\Http\Interface\Repository\IRepository;
use App\Models\Category;

class CategoryRepository implements IRepository
{
    private Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index(): Category
    {
        return $this->category;
    }

    public function store(array $data): void
    {
        $this->category->create($data);
    }

    public function show(int $id): Category|null
    {
        return $this->category->find($id);
    }

    public function update(int $id): Category|null
    {
        $model = $this->category->find($id);
        return $model;
    }

    public function delete(int $id): Category|null
    {
        $model = $this->category->find($id);
        return $model;
    }
}
