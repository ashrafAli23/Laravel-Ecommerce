<?php

declare(strict_types=1);

namespace Modules\Media\Utils;

use Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class Thumbnail
{
    private ImageManager $imageManager;

    private string $imagePath;

    private float $thumbRate;

    private string|int $thumbWidth;

    private string|int $thumbHeight;

    private string $destinationPath;

    private ?string $xCoordinate;

    private ?string $yCoordinate;

    private string $fitPosition;

    private string $fileName;

    private UploadManager $uploadManager;

    public function __construct(UploadManager $uploadManager, ImageManager $imageManager)
    {
        $this->thumbRate = 0.75;
        $this->xCoordinate = null;
        $this->yCoordinate = null;
        $this->fitPosition = 'center';

        $driver = 'gd';
        if (extension_loaded('imagick')) {
            $driver = 'imagick';
        }

        $this->imageManager = $imageManager->configure(compact('driver'));
        $this->uploadManager = $uploadManager;
    }

    public function setImage(string $imagePath): Thumbnail
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getImage(): string
    {
        return $this->imagePath;
    }

    public function setSize(string|int $width, string|int $height = 'auto'): Thumbnail
    {
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;

        if (!$height || $height == 'auto') {
            $this->thumbHeight = 0;
        } elseif ($height == 'rate') {
            $this->thumbHeight = (int)($this->thumbWidth * $this->thumbRate);
        }

        if (!$width || $width == 'auto') {
            $this->thumbWidth = 0;
        } elseif ($width == 'rate') {
            $this->thumbWidth = (int)($this->thumbHeight * $this->thumbRate);
        }
        return $this;
    }

    public function getSize(): array
    {
        return [$this->thumbWidth, $this->thumbHeight];
    }

    public function setDestinationPath(string $destinationPath): Thumbnail
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }

    public function setCoordinates(string $xCoordination, string $yCoordination): Thumbnail
    {
        $this->xCoordinate = $xCoordination;
        $this->yCoordinate = $yCoordination;

        return $this;
    }
    public function getCoordinates(): array
    {
        return [$this->xCoordinate, $this->yCoordinate];
    }

    public function setFileName(string $fileName): Thumbnail
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function save(string $type = 'fit')
    {
        $fileName = pathinfo($this->imagePath, PATHINFO_BASENAME);
        if ($this->fileName) {
            $fileName = $this->fileName;
        }

        $destinationPath = sprintf('%s/%s', trim($this->destinationPath, '/'), $fileName);

        $thumbImage = $this->imageManager->make($this->imagePath);

        if ($this->thumbWidth && !$this->thumbHeight) {
            $type = 'width';
        } elseif ($this->thumbHeight && !$this->thumbWidth) {
            $type = 'height';
        }

        switch ($type) {
            case 'width':
                $thumbImage->resize($this->thumbWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                break;

            case 'height':
                $thumbImage->resize(null, $this->thumbHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                break;

            case 'resize':
                $thumbImage->resize($this->thumbWidth, $this->thumbHeight);
                break;

            case 'crop':
                $thumbImage->crop($this->thumbWidth, $this->thumbHeight, $this->xCoordinate, $this->yCoordinate);

                break;

            case 'fit':
            default:
                if (extension_loaded('exif')) {
                    $thumbImage->orientate();
                }

                $thumbImage->fit($this->thumbWidth, $this->thumbHeight, null, $this->fitPosition);

                break;
        }
        try {
            $this->uploadManager->saveFile($destinationPath, $thumbImage->stream()->__toString());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }

        return $destinationPath;
    }
}
