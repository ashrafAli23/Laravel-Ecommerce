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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mimey\MimeTypes;
use Modules\Media\Dto\MediaFolderDto;
use Modules\Media\Entities\File as EntitiesFile;
use Modules\Media\Repositories\Interfaces\IMediaFileRepository;
use Modules\Media\Repositories\Interfaces\IMediaFolderRepository;
use Modules\Media\Services\MediaFolderService;
use Modules\Media\Transformers\FileTransformer;
use Throwable;

class Media
{
    /**
     * @param UploadManager $uploadManager
     * @param IMediaFileRepository $fileRepository
     * @param IMediaFolderRepository $mediaFolderRepository
     * @param MediaFolderService $mediaFolderService
     * @param Thumbnail $thumbnail
     */
    public function __construct(
        private readonly UploadManager $uploadManager,
        private readonly IMediaFileRepository $fileRepository,
        private readonly IMediaFolderRepository $mediaFolderRepository,
        private readonly MediaFolderService $mediaFolderService,
        private readonly Thumbnail $thumbnail,

    ) {
    }

    public function getSizes(): array
    {
        $sizes = $this->getConfig('sizes', []);
        foreach ($sizes as $name => $size) {
            $size = explode('x', $size);
            $width = $size[0];
            $height = $size[1];

            $sizes[$name] = $width . 'x' . $height;
        }

        return $sizes;
    }


    public function humanFilesize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }


    public function getAllImageSizes(?string $url): array
    {
        $images = [];
        foreach ($this->getSizes() as $size) {
            $readableSize = explode('x', $size);
            $images = $this->getImageUrl($url, $readableSize);
        }

        return $images;
    }

    public function getConfig($key = null, $default = null)
    {
        $configs = Config::get('media');
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
        $sizes = $this->getSizes();
        Arr::forget($sizes, $name);

        config(['media.media.sizes' => $sizes]);

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

    public function imageValidationRule(): string
    {
        return 'required|image|mimes:jpg,jpeg,png,webp,gif,bmp';
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

    public function getDefaultImage(bool $relative = false): string
    {
        $default = $this->getConfig('default_image');

        if ($relative) {
            return $default;
        }

        return $default ? url($default) : $default;
    }

    public function url(?string $path): string
    {
        $path = trim($path);
        if (Str::contains($path, 'https://') || Str::contains($path, 'http://')) {
            return $path;
        }

        return Storage::url($path);
    }

    public function getSize(string $name): ?string
    {
        return Arr::get($this->getSizes(), $name);
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
            throw new Exception("Invalid url", 400);
        }

        $info = pathinfo($url);
        $contents = file_get_contents($url);

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

    public function uploadFromPath(string $path, int $folderId = 0, ?string $folderSlug = null, ?string $defaultMimetype = null)
    {
        if (empty($path)) {
            return [
                'error' => true,
                'message' => "path invalid",
            ];
        }

        $mimeType = $this->getMimeType($path);

        if (empty($mimeType)) {
            $mimeType = $defaultMimetype;
        }

        $fileName = File::name($path);
        $fileExtension = File::extension($path);
        if (empty($fileExtension)) {
            $mimeTypeDetection = new MimeTypes();

            $fileExtension = $mimeTypeDetection->getExtension($mimeType);
        }

        $fileUpload = new UploadedFile($path, $fileName . '.' . $fileExtension, $mimeType, null, true);

        return $this->handleUpload($fileUpload, $folderId, $folderSlug);
    }

    public function deleteFile(EntitiesFile $file): bool
    {
        $this->deleteThumbnails($file);

        return Storage::delete($file->url);
    }

    public function deleteThumbnails(EntitiesFile $file): bool
    {
        if (!$file->canGenerateThumbnails()) {
            return false;
        }

        $filename = pathinfo($file->url, PATHINFO_FILENAME);

        $files = [];
        foreach ($this->getSizes() as $size) {
            $files[] = str_replace($filename, $filename . '-' . $size, $file->url);
        }

        return Storage::delete($files);
    }

    public function handleUpload(?UploadedFile $fileUpload, ?int $folderId, ?string $folderSlug = null, bool $skipValidation = false)
    {
        $request = request();
        if ($request->path) {
            $folderId = $this->handleTargetFolder($folderId, $request->path);
        }

        if (!$fileUpload) {
            throw new Exception("can not detect file type");
        }

        $allowedMimeTypes = $this->getConfig('allowed_mime_types');

        if (!$this->isChunkUploadEnabled()) {
            if (!$skipValidation) {
                $validator = Validator::make(['uploaded_file' => $fileUpload], [
                    'uploaded_file' => 'required|mimes:' . $allowedMimeTypes,
                ]);

                if ($validator->fails()) {
                    throw new Exception($validator->getMessageBag()->first());
                }
            }

            $maxUploadFilesizeAllowed = env('MAX_UPLOAD_FILE_SIZE', $this->getServerConfigMaxUploadFileSize());

            if ($maxUploadFilesizeAllowed && ($fileUpload->getSize() / 1024) / 1024 > (float)$maxUploadFilesizeAllowed) {
                throw new Exception("file too big readable size " . $maxUploadFilesizeAllowed * 1024 * 1024);
            }

            $maxSize = $this->getServerConfigMaxUploadFileSize();
            if ($fileUpload->getSize() / 1024 > (int)$maxSize) {
                throw new Exception("file too big readable size " . $maxSize);
            }
        }


        $file = $this->fileRepository->getModel();

        $fileExtension = $fileUpload->getClientOriginalExtension();

        if (!$skipValidation && !in_array(strtolower($fileExtension), explode(',', $allowedMimeTypes))) {
            throw new Exception("can not detect file type");
        }

        if ($folderId == 0 && !empty($folderSlug)) {
            $folder = $this->mediaFolderService->findOne(['slug' => $folderSlug]);

            if (!$folder) {
                $folder = $this->mediaFolderService->create(MediaFolderDto::create($folderSlug, Auth::id(), null));
            }

            $folderId = $folder->id;
        }

        $file->name = $this->fileRepository->createName(
            File::name($fileUpload->getClientOriginalName()),
            $folderId
        );

        $folderPath = $this->mediaFolderRepository->getFullPath($folderId);


        $fileName = $this->fileRepository->createSlug(
            $file->name,
            $fileExtension,
            Storage::path($folderPath ?: '')
        );

        $filePath = $fileName;

        if ($folderPath) {
            $filePath = $folderPath . '/' . $filePath;
        }


        $content = File::get($fileUpload->getRealPath());

        $this->uploadManager->saveFile($filePath, $content, $fileUpload);

        $data = $this->uploadManager->fileDetails($filePath);

        if (!$skipValidation && empty($data['mime_type'])) {
            throw new Exception("can not detect file type");
        }

        $file->url = $data['url'];
        $file->size = $data['size'];
        $file->mime_type = $data['mime_type'];
        $file->folder_id = $folderId;
        $file->user_id = Auth::check() ? Auth::id() : 0;
        $file->options = $request->options;
        $file = $this->fileRepository->createOrUpdate($file);
        $this->generateThumbnails($file);
        return $file;
    }

    public function canGenerateThumbnails(?string $mimeType): bool
    {
        if (!$this->getConfig('generate_thumbnails_enabled')) {
            return false;
        }

        if (!$mimeType) {
            return false;
        }

        return $this->isImage($mimeType) && !in_array($mimeType, ['image/svg+xml', 'image/x-icon']);
    }

    public function generateThumbnails(EntitiesFile $file): bool
    {
        if (!$file->canGenerateThumbnails()) {
            return false;
        }

        foreach ($this->getSizes() as $size) {
            $readableSize = explode('x', $size);

            $this->thumbnail
                ->setImage($this->getRealPath($file->url))
                ->setSize($readableSize[0], $readableSize[1])
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '-' . $size . '.' . File::extension($file->url))
                ->save();
        }


        return true;
    }

    public function getMimeType(string $url)
    {
        if (!isset($url)) {
            return null;
        }

        $mimeTypeDetection = new MimeTypes();

        return $mimeTypeDetection->getMimeType(File::extension($url));
    }

    private function getUploadPath(): string
    {
        return is_link(public_path('storage')) ? storage_path('app/public') : public_path('storage');
    }

    private function getUploadURL(): string
    {
        return str_replace('/index.php', '', $this->getConfig('default_upload_url'));
    }

    public function isChunkUploadEnabled(): bool
    {
        return $this->getConfig('chunk.enabled') == '1';
    }

    public function createFolder(string $folderSlug, ?int $parentId = 0)
    {
        $folder = $this->mediaFolderService->findOne(['slug' => $folderSlug, 'parent_id' => $parentId]);

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
