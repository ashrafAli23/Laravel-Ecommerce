<?php

declare(strict_types=1);

namespace Modules\Media\Services;

use Modules\Media\Dto\MediaFolderDto;
use Modules\Media\Repositories\Interfaces\IMediaFoldersRepository;

class MediaFolderService
{
    /**
     * @param IMediaFoldersRepository $mediaFolders
     */
    public function __construct(
        private readonly IMediaFoldersRepository $mediaFoldersRepository,
    ) {
    }

    public function create(MediaFolderDto $mediaFolderDto)
    {
        $media = $this->mediaFoldersRepository->createOrUpdate([
            'name' => $this->mediaFoldersRepository->createName($mediaFolderDto->name, $mediaFolderDto->parent_id),
            'slug' =>  $this->mediaFoldersRepository->createSlug($mediaFolderDto->slug, $mediaFolderDto->parent_id),
            'parent_id' => $mediaFolderDto->parent_id,
            'user_id' => $mediaFolderDto->user_id
        ]);

        return $media;
    }
}