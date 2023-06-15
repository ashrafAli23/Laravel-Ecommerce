<?php

declare(strict_types=1);

namespace Modules\Media\Repositories\Eloquent;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Common\Repositories\BaseRepository;
use Modules\Media\Facades\MediaFacade;
use Modules\Media\Repositories\Interfaces\IMediaFileRepository;

class MediaFileRepository extends BaseRepository implements IMediaFileRepository
{

    public function createName(string $name, string|int $folder)
    {
        $index = 1;
        $baseName = $name;
        while ($this->checkIfExistsName($name, $folder)) {
            $name = $baseName . '-' . $index++;
        }

        return $name;
    }

    public function checkIfExistsName(?string $name, ?int $folder): bool
    {
        $count = $this->model
            ->where('name', $name)
            ->where('folder_id', $folder)
            ->withTrashed()
            ->count();

        return $count > 0;
    }

    public function createSlug(string $name, string $extension, string $folderPath): string
    {
        $slug = Str::slug($name);
        $index = 1;
        $baseSlug = $slug;
        while (File::exists(MediaFacade::getRealPath(rtrim($folderPath, '/') . '/' . $slug . '.' . $extension))) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = $slug . '-' . time();
        }

        return $slug . '.' . $extension;
    }
}
