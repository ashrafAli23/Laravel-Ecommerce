<?php

namespace Modules\Media\Utils;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mimey\MimeTypes;
use Modules\Media\Facades\MediaFacade;

class UploadManager
{
    public function __construct(private readonly MimeTypes $mimeType)
    {
    }

    public function fileMimeType(string $path): ?string
    {
        return $this->mimeType->getMimeType(File::extension(MediaFacade::getRealPath($path)));
    }

    public function fileDetails(string $path): array
    {
        return [
            'filename' => File::basename($path),
            'url' => $path,
            'mime_type' => $this->fileMimeType($path),
            'size' => $this->fileSize($path),
            'modified' => $this->fileModified($path),
        ];
    }

    public function fileSize(string $path): int
    {
        return Storage::size($path);
    }

    public function fileModified(string $path): string
    {
        return Carbon::createFromTimestamp(Storage::lastModified($path));
    }

    public function createDirectory(string $folder)
    {
        $folder = $this->cleanFolder($folder);

        if (Storage::exists($folder)) {
            return "this folder already exsits";
        }

        return Storage::makeDirectory($folder);
    }

    private function cleanFolder(string $folder): string
    {
        return DIRECTORY_SEPARATOR . trim(str_replace('..', '', $folder), DIRECTORY_SEPARATOR);
    }

    public function deleteDirectory(string $folder)
    {
        $folder = $this->cleanFolder($folder);

        $filesFolders = array_merge(Storage::directories($folder), Storage::files($folder));

        if (!empty($filesFolders)) {
            return "directory must empty";
        }

        return Storage::deleteDirectory($folder);
    }

    public function deleteFile(string $path): bool
    {
        $path = $this->cleanFolder($path);

        return Storage::delete($path);
    }

    public function saveFile(
        string $path,
        string $content,
        UploadedFile $file = null,
        array $visibility = ['visibility' => 'public']
    ): bool {

        if (!MediaFacade::isChunkUploadEnabled() || !$file) {
            return Storage::put($this->cleanFolder($path), $content, $visibility);
        }

        $currentChunksPath = MediaFacade::getConfig('chunk.storage.chunks') . '/' . $file->getFilename();
        $disk = Storage::disk(MediaFacade::getConfig('chunk.storage.disk'));

        try {
            $stream = $disk->getDriver()->readStream($currentChunksPath);

            if ($result = Storage::writeStream($path, $stream, $visibility)) {
                $disk->delete($currentChunksPath);
            }
        } catch (Exception $exception) {
            return Storage::put($this->cleanFolder($path), $content, $visibility);
        }

        return $result;
    }
}
