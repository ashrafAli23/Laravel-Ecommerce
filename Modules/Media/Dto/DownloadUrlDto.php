<?php

declare(strict_types=1);

namespace Modules\Media\Dto;


class DownloadUrlDto
{
    public function __construct(
        public readonly string $url,
        public readonly int $folderId,
    ) {
    }


    /**
     * @param string $url
     * @param integer $folderId
     * @return self
     */
    public static function create(string $url, int $folderId): self
    {
        return new self(url: $url, folderId: $folderId);
    }
}
