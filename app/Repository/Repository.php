<?php

declare(strict_types=1);

namespace App\Repository;

use App\Http\Interface\Repository\IRepository;
use Illuminate\Database\Eloquent\Model;

class Repository implements IRepository
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function index(): Model
    {
        return $this->model;
    }

    public function store(array $data): void
    {
        $this->model->create($data);
    }

    public function show(int $id): Model|null
    {
        return $this->model->find($id);
    }

    public function update(int $id): Model|null
    {
        $model = $this->model->find($id);
        return $model;
    }

    public function delete(int $id): Model|null
    {
        $model = $this->model->find($id);
        return $model;
    }
}
