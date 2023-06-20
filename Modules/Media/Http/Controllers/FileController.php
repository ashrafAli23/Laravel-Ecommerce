<?php

declare(strict_types=1);

namespace Modules\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Media\Dto\DownloadUrlDto;
use Modules\Media\Dto\UploadFileDto;
use Modules\Media\Http\Requests\FileDownloadRequest;
use Modules\Media\Http\Requests\UploadFilesRequest;
use Modules\Media\Services\MediaFileService;
use Modules\Media\Transformers\FileTransformer;

class FileController extends Controller
{
    public function __construct(
        private readonly MediaFileService $mediaFileService
    ) {
    }

    public function upload(UploadFilesRequest $request, BaseResponse $response)
    {
        try {
            $result = $this->mediaFileService->upload(UploadFileDto::create(
                $request->file('file'),
                (int)$request->folder_id
            ));

            return $response
                ->setMessage("Upload successfully")
                ->setData(new FileTransformer($result))->toApiResponse();

            // Create the file receiver
            // $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // // Check if the upload is success, throw exception or return response you need
            // if ($receiver->isUploaded() === false) {
            //     throw new UploadMissingFileException();
            // }
            // // Receive the file
            // $save = $receiver->receive();
            // // Check if the upload has finished (in chunk mode it will send smaller files)
            // if ($save->isFinished()) {
            //     $result = MediaFacade::handleUpload($save->getFile(), $request->folder_id);

            //     return $this->handleUploadResponse($result);
            // }
            // // We are in chunk mode, lets send the current progress
            // $handler = $save->handler();

            // return $response->setData([
            //     'done' => $handler->getPercentageDone(),
            // ])->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode(400)
                ->toApiResponse();
        }
    }

    public function uploadUrl(FileDownloadRequest $request, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $result = $this->mediaFileService->uploadUrl(DownloadUrlDto::create(
                $request->url,
                $request->folder_id
            ));

            return $response
                ->setMessage('Uploaded successfully')
                ->setData(new FileTransformer($result))->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode($th->getCode())
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }
}
