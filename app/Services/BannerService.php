<?php

declare(strict_types=1);

namespace App\Services;

use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;

class BannerService
{
    use UploadFile;

    public function getAllBanners(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $data = $this->repository->index()
            ->query()->paginate($entries);

        if (!$data) {
            throw new Exception("Banners is empty", 400);
        }

        return $data;
    }

    public function storeBanner(Request $request): void
    {
        $this->repository->store([
            'banner_type' => $request->banner_type,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $this->uploadFile($request, 'image', 'banner'),
            'active' => $request->status ?? 1,
        ]);
    }

    public function showBanner(int $id): object
    {
        $data = $this->repository->show($id);

        if (!$data) {
            throw new Exception("Banner not found", 404);
        }

        return $data;
    }

    public function updateBanner(int $id, Request $request): void
    {

        $data = $this->repository->update($id);

        if (!$data) {
            throw new Exception("Banner not found", 404);
        }

        if (isset($request->status) && !isset($request->title)) {
            $data->update(['active' => $request->status]);
        } else {

            $requestData = [
                'banner_type' => $request->banner_type ?? $data->banner_type,
                'title' => $request->title ?? $data->title,
                'description' => $request->description ?? $data->description,
                'active' => $request->status ?? 1,
            ];

            $data->update($requestData);
        }
    }

    public function updateBannerIMG(int $id, Request $request): void
    {
        $data = $this->repository->update($id);

        if (!$data) {
            throw new Exception("Banner not found", 404);
        }

        $this->deleteFile($data->image);

        $requestData = [
            'image' => $this->uploadFile($request, 'banner')
        ];

        $data->update($requestData);
    }

    public function deleteBanner(int $id): void
    {
        $data = $this->repository->delete($id);
        if (!$data) {
            throw new Exception("Banner not found", 404);
        }
        $this->deleteFile($data->image);
        $data->delete();
    }
}