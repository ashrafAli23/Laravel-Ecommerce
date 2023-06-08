<?php

declare(strict_types=1);

namespace Modules\Media\Utils;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mimey\MimeTypes;

class Media
{
    // private array $permissions = [];
    public function __construct()
    {
        // $this->permissions = $this->getConfig('permissions');
    }

    // public function getSizes(): array
    // {
    //     $sizes = $this->getConfig('sizes', []);

    //     foreach ($sizes as $name => $size) {
    //         $size = explode('x', $size);

    //         $settingName = 'media_sizes_' . $name;

    //         $width = setting($settingName . '_width', $size[0]);

    //         $height = setting($settingName . '_height', $size[1]);

    //         if (!$width) {
    //             $width = 'auto';
    //         }

    //         if (!$height) {
    //             $height = 'auto';
    //         }

    //         $sizes[$name] = $width . 'x' . $height;
    //     }

    //     return $sizes;
    // }

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
}
