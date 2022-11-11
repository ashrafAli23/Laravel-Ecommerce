<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BannerRequest;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Resources\BannerResource;
use App\Services\BannerService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as STATUS;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use Response;

    private BannerService $banner;
    public function __construct(BannerService $banner)
    {
        $this->banner = $banner;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $data = $this->banner->getAllBanners($request);
            return $this
                ->dataResponse(BannerResource::collection($data), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerRequest $request): JsonResponse
    {
        try {
            $this->banner->storeBanner($request);
            return $this->successResponse("Banner created successfully", STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): JsonResponse
    {
        try {
            $banner = $this->banner->showBanner($id);

            return $this->dataResponse(new BannerResource($banner), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'banner_type' => 'required|string|min:3',
                'title' => 'required|string|min:3',
                'description' => 'required|string',
                'active' => 'nullable|boolean'
            ]);

            $this->banner->updateBanner($id, $request);
            return $this->successResponse("Banner updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'active' => 'required|boolean'
            ]);

            $this->banner->updateBanner($id, $request);
            return $this->successResponse("Banner updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function updateImage(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image'
            ]);

            $this->banner->updateBannerIMG($id, $request);

            return $this->successResponse("Banner updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->banner->deleteBanner($id);
            return $this->successResponse("Banner deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }
}
