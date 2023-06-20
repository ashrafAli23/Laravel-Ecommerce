<?php

declare(strict_types=1);

namespace Modules\Media\Dto;

use Illuminate\Http\UploadedFile;

class UploadFileDto
{
    public function __construct(
        public readonly UploadedFile|array $file,
        public readonly ?int $folderId,
    ) {
    }

    /**
     * @param UploadedFile|array $file
     * @param integer $folderId
     * @return self
     */
    public static function create(UploadedFile|array $file, ?int $folderId): self
    {
        return new self($file, $folderId);
    }
}
