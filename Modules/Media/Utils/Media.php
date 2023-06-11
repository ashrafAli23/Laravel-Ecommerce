<?php

declare(strict_types=1);

namespace Modules\Media\Utils;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mimey\MimeTypes;
use Modules\Media\Dto\MediaFolderDto;
use Modules\Media\Repositories\Interfaces\IMediaFileRepository;
use Modules\Media\Services\MediaFolderService;

class Media
{

    public function __construct(
        private readonly UploadManager $uploadManager,
        private readonly IMediaFileRepository $fileRepository,
        private readonly MediaFolderService $mediaFolderService,
        private readonly Thumbnail $thumbnail,

    ) {
        // $this->permissions = $this->getConfig('permissions');
    }

    // public function getAllImageSizes(?string $url): array
    // {
    //     $images = [];
    //     foreach ($this->getSizes() as $size) {
    //         $readableSize = explode('x', $size);
    //         $images = $this->getImageUrl($url, $readableSize);
    //     }

    //     return $images;
    // }

    public function getConfig($key = null, $default = null)
    {
        $configs = Config::get('media.media');

        if (!$key) {
            return $configs;
        }

        return Arr::get($configs, $key, $default);
    }

    public function addSize(string $name, $width, $height = 'auto'): self
    {
        if (!$width) {
            $width = 'auto';
        }

        if (!$height) {
            $height = 'auto';
        }

        Config::get(['media.media.sizes.' . $name => $width . 'x' . $height]);

        return $this;
    }

    public function removeSize(string $name): self
    {
        // $sizes = $this->getSizes();
        // Arr::forget($sizes, $name);

        // config(['media.media.sizes' => $sizes]);

        return $this;
    }

    public function getServerConfigMaxUploadFileSize(): float
    {
        $maxSize = $this->parseSize(ini_get('post_max_size'));
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        if ($uploadMax > 0 && $uploadMax < $maxSize) {
            $maxSize = $uploadMax;
        }

        return $maxSize;
    }

    public function getImageUrl(?string $url, $size = null, bool $relativePath = false, $default = null)
    {
        if (empty($url)) {
            return $default;
        }

        $url = trim($url);

        if (empty($url)) {
            return $default;
        }

        if (empty($size) || $url == '__value__') {
            if ($relativePath) {
                return $url;
            }

            return $this->url($url);
        }

        if ($url == $this->getDefaultImage()) {
            return url($url);
        }

        if (
            array_key_exists($size, $this->getSizes()) &&
            $this->canGenerateThumbnails($this->getMimeType($url))
        ) {
            $url = str_replace(
                File::name($url) . '.' . File::extension($url),
                File::name($url) . '-' . $this->getSize($size) . '.' . File::extension($url),
                $url
            );
        }

        if ($relativePath) {
            return $url;
        }

        if ($url == '__image__') {
            return $this->url($default);
        }

        return $this->url($url);
    }

    public function parseSize($size): float
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round((float)$size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round((float)$size);
    }

    public function getRealPath(string $url): string
    {
        switch (Config::get('filesystems.default')) {
            case 'local':
            case 'public':
                return Storage::path($url);
            default:
                return Storage::url($url);
        }
    }

    public function isImage(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'image/');
    }

    public function isUsingCloud(): bool
    {
        return !in_array(config('filesystems.default'), ['local', 'public']);
    }

    public function uploadFromUrl(string $url, int $folderId = 0, ?string $folderSlug = null, ?string $defaultMimetype = null)
    {
        if (empty($url)) {
            return [
                'error' => true,
                'message' => "Invalid url",
            ];
        }

        $info = pathinfo($url);

        try {
            $contents = file_get_contents($url);
        } catch (Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }

        if (empty($contents)) {
            return null;
        }

        $path = '/tmp';
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755);
        }

        $path = $path . '/' . $info['basename'];
        file_put_contents($path, $contents);

        $mimeType = $this->getMimeType($url);

        if (empty($mimeType)) {
            $mimeType = $defaultMimetype;
        }

        $fileName = File::name($info['basename']);
        $fileExtension = File::extension($info['basename']);
        if (empty($fileExtension)) {
            $mimeTypeDetection = new MimeTypes();

            $fileExtension = $mimeTypeDetection->getExtension($mimeType);
        }

        $fileUpload = new UploadedFile($path, $fileName . '.' . $fileExtension, $mimeType, null, true);

        $result = $this->handleUpload($fileUpload, $folderId, $folderSlug);

        File::delete($path);

        return $result;
    }

    public function getMimeType(string $url)
    {
        if (!isset($url)) {
            return null;
        }

        $mimeTypeDetection = new MimeTypes();

        return $mimeTypeDetection->getMimeType(File::extension($url));
    }

    public function isChunkUploadEnabled(): bool
    {
        return $this->getConfig('chunk.enabled') == '1';
    }

    public function createFolder(string $folderSlug, ?int $parentId = 0)
    {
        $folder = $this->mediaFolderService->findOne($folderSlug, $parentId);

        if (!$folder) {
            $folder = $this->mediaFolderService->create(MediaFolderDto::create($folderSlug, Auth::id(), $parentId));
        }

        return $folder->id;
    }

    public function handleTargetFolder(?int $folderId = 0, string $filePath = ''): string
    {
        if (strpos($filePath, '/') !== false) {
            $paths = explode('/', $filePath);
            array_pop($paths);
            foreach ($paths as $folder) {
                $folderId = $this->createFolder($folder, $folderId);
            }
        }

        return $folderId;
    }
}
