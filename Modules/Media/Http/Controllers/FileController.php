<?php

declare(strict_types=1);

namespace Modules\Media\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Media\Dto\DownloadUrlDto;
use Modules\Media\Dto\UploadFileDto;
use Modules\Media\Facades\MediaFacade;
use Modules\Media\Http\Requests\FileDownloadRequest;
use Modules\Media\Services\MediaFileService;

class FileController extends Controller
{
    public function __construct(
        private readonly MediaFileService $mediaFileService
    ) {
    }

    public function upload(Request $request, BaseResponse $response)
    {


        try {
            $result = $this->mediaFileService->upload(UploadFileDto::create(
                $request->file('file'),
                $request->folder_id
            ));

            return $response
                ->setMessage("Upload successfully")
                ->setData([
                    'id' => $result['data']->id,
                    'src' => MediaFacade::url($result['data']->url)
                ])->toApiResponse();

            // Create the file receiver
            $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // Check if the upload is success, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }
            // Receive the file
            $save = $receiver->receive();
            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                $result = MediaFacade::handleUpload($save->getFile(), $request->folder_id);

                return $this->handleUploadResponse($result);
            }
            // We are in chunk mode, lets send the current progress
            $handler = $save->handler();

            return $response->setData([
                'done' => $handler->getPercentageDone(),
            ])->toApiResponse();
        } catch (Exception $ex) {
            return $response
                ->setSuccess(false)
                ->setMessage($ex->getMessage())
                ->setCode(400)
                ->toApiResponse();
        }
    }

    public function downloadUrl(FileDownloadRequest $request, BaseResponse $response)
    {
        try {
            $result = $this->mediaFileService->downloadUrl(DownloadUrlDto::create(
                $request->url,
                $request->folder_id
            ));

            return $response
                ->setMessage('Uploaded successfully')
                ->setData([
                    'id' => $result['data']->id,
                    'src' => Storage::url($result['data']->url),
                    'url' => $result['data']->url,
                ])->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode(500)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }
}
