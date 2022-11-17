<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Resources\ChattResource;
use App\Services\ChattingService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as STATUS;

class ChattController extends Controller
{
    use Response;
    private ChattingService $chatt;
    public function __construct(ChattingService $chatt)
    {
        $this->chatt = $chatt;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $data = $this->chatt->getAllMessages($request);
            return $this
                ->dataResponse(ChattResource::collection($data), STATUS::HTTP_OK);
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
            $data = $this->chatt->showMessage($id);
            return $this->dataResponse(new ChattResource($data), STATUS::HTTP_OK);
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
            $this->chatt->deleteMessage($id);
            return $this->successResponse("Message deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }
}
