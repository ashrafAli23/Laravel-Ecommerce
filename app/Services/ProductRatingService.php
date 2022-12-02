<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\Repository\IRepository;
use Exception;
use Illuminate\Http\Request;

class ProductRatingService
{
    private IRepository $repository;

    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllRating(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $data = $this->repository->index()
            ->query()->paginate($entries);

        if (!$data) {
            throw new Exception("Rating is empty", 400);
        }

        return $data;
    }

    public function storeRating(Request $request): void
    {
        $this->repository->store([
            'product_id' => $request->product_id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'rating' => $request->rating,
        ]);
    }

    public function showRate(int $id): object
    {
        $data = $this->repository->show($id);

        if (!$data) {
            throw new Exception("Rating not found", 404);
        }

        return $data;
    }

    public function updateRate(int $id, Request $request): void
    {
        $data = $this->repository->update($id);
        if (!$data) {
            throw new Exception("Rate not found", 404);
        }
        $data->update([
            'message' => $request->message,
            'rating' => $request->rating
        ]);
    }

    public function deleteRate(int $id): void
    {
        $data = $this->repository->delete($id);
        if (!$data) {
            throw new Exception("Rate not found", 404);
        }

        $data->delete();
    }
}
