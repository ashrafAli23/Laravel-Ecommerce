<?php

namespace Modules\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Media\Dto\ActionDto;
use Modules\Media\Http\Requests\ActionRequest;
use Modules\Media\Services\MediaService;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }
    public function action(ActionRequest $request, BaseResponse $response): JsonResponse
    {
        try {
            $result = $this->mediaService
                ->action(ActionDto::create(
                    $request->selected,
                    $request->action,
                ));

            return $response->setMessage($result)->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode($th->getCode())
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }
}