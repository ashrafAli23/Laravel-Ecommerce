<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Requests\V1\UnitsRequest;
use App\Http\Resources\UnitsResource;
use App\Services\UnitsService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as STATUS;

class UnitsController extends Controller
{
    use Response;

    private UnitsService $units;
    public function __construct(UnitsService $units)
    {
        $this->units = $units;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $data = $this->units->getAllUnits($request);
            return $this
                ->dataResponse(UnitsResource::collection($data), STATUS::HTTP_OK);
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
    public function store(UnitsRequest $request): JsonResponse
    {
        try {
            $this->units->storeUnits($request);
            return $this->successResponse("Units created successfully", STATUS::HTTP_CREATED);
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
            $units = $this->units->showUnit($id);

            return $this->dataResponse(new UnitsResource($units), STATUS::HTTP_OK);
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
    public function update(UnitsRequest $request, int $id): JsonResponse
    {
        try {
            $this->units->updateUnit($request, $id);
            return $this->successResponse("Unit updated successfully", STATUS::HTTP_OK);
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
            $this->units->deleteUnit($id);
            return $this->successResponse("Unit deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }
}