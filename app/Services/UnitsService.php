<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\Repository\IRepository;
use Exception;
use Illuminate\Http\Request;

class UnitsService
{
    private IRepository $repository;
    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllUnits(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $data = $this->repository->index()
            ->query()->paginate($entries);

        if (!$data) {
            throw new Exception("Units is empty", 400);
        }

        return $data;
    }

    public function storeUnits(Request $request): void
    {
        $this->repository->store([
            'name' => $request->name
        ]);
    }

    public function showUnit(int $id): object
    {
        $data = $this->repository->show($id);

        if (!$data) {
            throw new Exception("Unit not found", 404);
        }

        return $data;
    }

    public function updateUnit(Request $request, int $id): void
    {
        $data = $this->repository->update($id);

        if (!$data) {
            throw new Exception("Unit not found", 404);
        }

        $data->update([
            'name' => $request->name
        ]);
    }

    public function deleteUnit(int $id): void
    {
        $data = $this->repository->delete($id);
        if (!$data) {
            throw new Exception("Unit not found", 404);
        }
        $data->delete();
    }
}