<?php

declare(strict_types=1);

namespace Modules\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Media\Dto\MediaFolderDto;
use Modules\Media\Http\Requests\FolderRequest;
use Modules\Media\Services\MediaFolderService;
use Modules\Media\Transformers\FolderTransformer;
use Symfony\Component\HttpFoundation\Response;

class FolderController extends Controller
{
    public function __construct(
        private readonly MediaFolderService $mediaFolderService
    ) {
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param FolderRequest $request
     * @param BaseResponse $response
     * @return JsonResponse
     */
    public function store(FolderRequest $request, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $media = $this->mediaFolderService->create(MediaFolderDto::create(
                $request->name,
                $request->user()->id,
                $request->parent_id
            ));

            return $response
                ->setCode(Response::HTTP_CREATED)
                ->setMessage("Created succesfully")
                ->setData(new FolderTransformer($media))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage("Failed to create")
                ->toApiResponse();
        }
    }
}
