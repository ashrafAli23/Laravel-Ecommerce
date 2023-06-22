<?php

namespace Modules\Media\Utils\Zip;

use Exception;
use ZipArchive;

class ZipRepository
{
    private ZipArchive $archive;

    public function __construct(string $filePath, bool $create = false, ZipArchive $archive = null)
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('Error: Your PHP version is not compiled with zip support');
        }

        $this->archive = $archive ?: new ZipArchive();

        $res = $this->archive->open($filePath, ($create ? ZipArchive::CREATE : null));

        if ($res !== true) {
            throw new Exception('Error: Failed to open ' . $filePath . '! Error: ' . $this->getErrorMessage($res));
        }
    }

    /**
     * @param $resultCode
     * @return string
     */
    private function getErrorMessage(int $resultCode)
    {
        switch ($resultCode) {
            case ZipArchive::ER_EXISTS:
                return 'ZipArchive::ER_EXISTS - File already exists.';
            case ZipArchive::ER_INCONS:
                return 'ZipArchive::ER_INCONS - Zip archive inconsistent.';
            case ZipArchive::ER_MEMORY:
                return 'ZipArchive::ER_MEMORY - Malloc failure.';
            case ZipArchive::ER_NOENT:
                return 'ZipArchive::ER_NOENT - No such file.';
            case ZipArchive::ER_NOZIP:
                return 'ZipArchive::ER_NOZIP - Not a zip archive.';
            case ZipArchive::ER_OPEN:
                return 'ZipArchive::ER_OPEN - Can\'t open file.';
            case ZipArchive::ER_READ:
                return 'ZipArchive::ER_READ - Read error.';
            case ZipArchive::ER_SEEK:
                return 'ZipArchive::ER_SEEK - Seek error.';
            default:
                return 'An unknown error [' . $resultCode . '] has occurred.';
        }
    }

    /**
     * Add a file to the opened Archive
     *
     * @param string $pathToFile
     * @param string $pathInArchive
     */
    public function addFile(string $pathToFile, string $pathInArchive)
    {
        $this->archive->addFile($pathToFile, $pathInArchive);
    }

    /**
     * Add a file to the opened Archive using its contents
     *
     * @param string $name
     * @param string $content
     */
    public function addFromString(string $name, string $content)
    {
        $this->archive->addFromString($name, $content);
    }

    /**
     * Closes the archive and saves it
     */
    public function close()
    {
        @$this->archive->close();
    }
}