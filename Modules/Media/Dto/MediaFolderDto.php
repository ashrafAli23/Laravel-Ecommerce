<?php

declare(strict_types=1);

namespace Modules\Media\Dto;

use Illuminate\Support\Str;

class MediaFolderDto
{
    /**
     * @param string $name
     * @param integer $userId
     * @param integer $parentId
     */
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly int $userId,
        public readonly int $parentId,
    ) {
    }

    public static function create(string $name, int $userId, int $parentId): self
    {

        return new self(
            name: $name,
            slug: Str::slug($name),
            userId: $userId,
            parentId: $parentId
        );
    }
}
