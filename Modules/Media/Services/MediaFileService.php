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

    public function uploadUrl(DownloadUrlDto $downloadUrlDto)
    {
        $result = MediaFacade::uploadFromUrl($downloadUrlDto->url, $downloadUrlDto->folderId);
        return $result;
    }

    public function upload(UploadFileDto $uploadFileDto)
    {
        $result = MediaFacade::handleUpload($uploadFileDto->file, $uploadFileDto->folderId);
        return $result;
    }
}
