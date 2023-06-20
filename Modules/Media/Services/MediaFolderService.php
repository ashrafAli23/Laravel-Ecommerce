<?php

declare(strict_types=1);

namespace Modules\Media\Services;

use Modules\Media\Dto\MediaFolderDto;
use Modules\Media\Repositories\Interfaces\IMediaFolderRepository;

class MediaFolderService
{
    /**
     * @param IMediaFolderRepository $mediaFolders
     */
    public function __construct(
        private readonly IMediaFolderRepository $mediaFolderRepository,
    ) {
    }

    public function create(MediaFolderDto $mediaFolderDto)
    {
        $media = $this->mediaFolderRepository->createOrUpdate([
            'name' => $this->mediaFolderRepository->createName($mediaFolderDto->name, $mediaFolderDto->parentId),
            'slug' =>  $this->mediaFolderRepository->createSlug($mediaFolderDto->slug, $mediaFolderDto->parentId),
            'parent_id' => $mediaFolderDto->parentId,
            'user_id' => $mediaFolderDto->userId
        ]);
        return $media;
    }

    public function findOne(array $conditions)
    {
        $folder = $this->mediaFolderRepository->getFirstBy($conditions);

        return $folder;
    }
}
