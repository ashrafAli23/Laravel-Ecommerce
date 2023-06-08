<?php

declare(strict_types=1);

namespace Modules\Media\Repositories\Interfaces;

use Modules\Common\Repositories\Interfaces\IRepository;

interface IMediaFolderRepository extends IRepository
{
    /**
     * @param int $folderId
     * @param array $params
     * @param bool $withTrash
     * @return mixed
     */
    public function getFolderByParentId(int $folderId, array $params = [], bool $withTrash = false);

    /**
     * @param string $slug
     * @param int $parentId
     * @return string
     */
    public function createSlug(string $slug, int $parentId): string;

    /**
     * @param string $name
     * @param integer $parentId
     * @return string
     */
    public function createName(string $name, int $parentId): string;

    /**
     * @param int $parentId
     * @param array $breadcrumbs
     * @return array
     */
    public function getBreadcrumbs(int $parentId, array $breadcrumbs = []);

    /**
     * @param int $parentId
     * @param array $params
     * @return mixed
     */
    public function getTrashed(int $parentId, array $params = []);

    /**
     * @param int $folderId
     * @param bool $force
     */
    public function deleteFolder(int $folderId, bool $force = false);

    /**
     * @param int $parentId
     * @param array $child
     * @return array
     */
    public function getAllChildFolders(int $parentId, array $child = []);

    /**
     * @param int $folderId
     * @param string $path
     * @return string
     */
    public function getFullPath(int $folderId, string $path = ''): string;

    /**
     * @param int $folderId
     */
    public function restoreFolder(int $folderId);

    /**
     * @return bool
     */
    public function emptyTrash(): bool;
}
