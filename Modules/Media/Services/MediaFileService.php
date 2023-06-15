<?php

declare(strict_types=1);

namespace Modules\Media\Services;

use Exception;
use Illuminate\Support\Arr;
use Modules\Media\Dto\DownloadUrlDto;
use Modules\Media\Dto\UploadFileDto;
use Modules\Media\Facades\MediaFacade;
use Modules\Media\Repositories\Interfaces\IMediaFileRepository;

class MediaFileService
{
    public function __construct(
        private readonly IMediaFileRepository $mediaFileRepository
    ) {
    }

    public function downloadUrl(DownloadUrlDto $downloadUrlDto)
    {
        $result = MediaFacade::uploadFromUrl($downloadUrlDto->url, $downloadUrlDto->folderId);
        if (isset($result['error'])) {
            throw new Exception($result['message']);
        }
        return $result;
    }

    public function upload(UploadFileDto $uploadFileDto)
    {
        if (!MediaFacade::isChunkUploadEnabled()) {
            $result = MediaFacade::handleUpload(Arr::first($uploadFileDto->file), $uploadFileDto->folderId);

            if (isset($result['error'])) {
                throw new Exception($result['message']);

                // return $response
                //     ->setMessage("Upload successfully")
                //     ->setData([
                //         'id' => $result['data']->id,
                //         'src' => MediaFacade::url($result['data']->url)
                //     ])->toApiResponse();
            }
            return $result;
        }
    }
}
