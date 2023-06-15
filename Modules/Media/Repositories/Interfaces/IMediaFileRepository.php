<?php

declare(strict_types=1);

namespace Modules\Media\Repositories\Interfaces;

use Modules\Common\Repositories\Interfaces\IRepository;

interface IMediaFileRepository extends IRepository
{
    public function createName(string $name, string $folder);
    public function checkIfExistsName(?string $name, ?int $folder): bool;
    public function createSlug(string $name, string $extension, string $folderPath): string;
}
