<?php

declare(strict_types=1);

namespace Modules\Media\Dto;

use Illuminate\Support\Str;
use Modules\Media\Http\Requests\FolderRequest;

class MediaFolderDto
{
    /**
     * @param string $name
     * @param integer $user_id
     * @param integer $parent_id
     */
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly int $user_id,
        public readonly int $parent_id,
    ) {
    }

    /**
     * @param FolderRequest $request
     * @return self
     */
    public static function create(FolderRequest $request): self
    {

        return new self(
            name: $request->name,
            slug: Str::slug($request->name),
            user_id: $request->user()->id,
            parent_id: $request->parent_id
        );
    }
}