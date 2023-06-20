<?php

declare(strict_types=1);

namespace Modules\Media\Services;

use Exception;
use Modules\Media\Dto\ActionDto;
use Modules\Media\Repositories\Interfaces\IMediaFileRepository;
use Modules\Media\Repositories\Interfaces\IMediaFolderRepository;

class MediaService
{
    public function __construct(
        private readonly IMediaFileRepository $mediaFileRepository,
        private readonly IMediaFolderRepository $mediaFolderRepository
    ) {
    }

    public function action(ActionDto $actionDto)
    {
        switch ($actionDto->action) {
            case 'trash':
                foreach ($actionDto->listIds as $key) {
                    if (!$key['is_folder']) {
                        $this->mediaFileRepository->deleteBy(['id' => $key['id']]);
                    } else {
                        $this->mediaFolderRepository->deleteFolder($key['id']);
                    }
                }

                $response = "trash success";

                break;

                // case 'restore':
                //     $error = false;
                //     foreach ($request->input('selected') as $item) {
                //         $id = $item['id'];
                //         if ($item['is_folder'] == 'false') {
                //             try {
                //                 $this->fileRepository->restoreBy(['id' => $id]);
                //             } catch (Exception $exception) {
                //                 info($exception->getMessage());
                //                 $error = true;
                //             }
                //         } else {
                //             $this->folderRepository->restoreFolder($id);
                //         }
                //     }

                //     if ($error) {
                //         $response = RvMedia::responseError(trans('core/media::media.restore_error'));

                //         break;
                //     }

                //     $response = RvMedia::responseSuccess([], trans('core/media::media.restore_success'));

                //     break;

                // case 'make_copy':
                //     foreach ($request->input('selected', []) as $item) {
                //         $id = $item['id'];
                //         if ($item['is_folder'] == 'false') {
                //             $file = $this->fileRepository->getFirstBy(['id' => $id]);

                //             if (!$file) {
                //                 break;
                //             }

                //             $this->copyFile($file);
                //         } else {
                //             $oldFolder = $this->folderRepository->getFirstBy(['id' => $id]);

                //             if (!$oldFolder) {
                //                 break;
                //             }

                //             $folderData = $oldFolder->replicate()->toArray();

                //             $folderData['slug'] = $this->folderRepository->createSlug(
                //                 $oldFolder->name,
                //                 $oldFolder->parent_id
                //             );
                //             $folderData['name'] = $oldFolder->name . '-(copy)';
                //             $folderData['user_id'] = Auth::id();
                //             $folder = $this->folderRepository->create($folderData);

                //             $files = $this->fileRepository->getFilesByFolderId($id, [], false);
                //             foreach ($files as $file) {
                //                 $this->copyFile($file, $folder->id);
                //             }

                //             $children = $this->folderRepository->getAllChildFolders($id);
                //             foreach ($children as $parentId => $child) {
                //                 if ($parentId != $oldFolder->id) {
                //                     /**
                //                      * @var MediaFolder $child
                //                      */
                //                     $folder = $this->folderRepository->getFirstBy(['id' => $parentId]);

                //                     if (!$folder) {
                //                         break;
                //                     }

                //                     $folderData = $folder->replicate()->toArray();

                //                     $folderData['slug'] = $this->folderRepository->createSlug(
                //                         $oldFolder->name,
                //                         $oldFolder->parent_id
                //                     );
                //                     $folderData['name'] = $oldFolder->name . '-(copy)';
                //                     $folderData['user_id'] = Auth::id();
                //                     $folderData['parent_id'] = $folder->id;
                //                     $folder = $this->folderRepository->create($folderData);

                //                     $parentFiles = $this->fileRepository->getFilesByFolderId($parentId, [], false);
                //                     foreach ($parentFiles as $parentFile) {
                //                         $this->copyFile($parentFile, $folder->id);
                //                     }
                //                 }

                //                 foreach ($child as $sub) {
                //                     /**
                //                      * @var Eloquent $sub
                //                      */
                //                     $subFiles = $this->fileRepository->getFilesByFolderId($sub->id, [], false);

                //                     $subFolderData = $sub->replicate()->toArray();

                //                     $subFolderData['user_id'] = Auth::id();
                //                     $subFolderData['parent_id'] = $folder->id;

                //                     $sub = $this->folderRepository->create($subFolderData);

                //                     foreach ($subFiles as $subFile) {
                //                         $this->copyFile($subFile, $sub->id);
                //                     }
                //                 }
                //             }

                //             $allFiles = Storage::allFiles($this->folderRepository->getFullPath($oldFolder->id));
                //             foreach ($allFiles as $file) {
                //                 Storage::copy($file, str_replace($oldFolder->slug, $folder->slug, $file));
                //             }
                //         }
                //     }

                //     $response = RvMedia::responseSuccess([], trans('core/media::media.copy_success'));

                //     break;

            case 'delete':
                foreach ($actionDto->listIds as $key) {
                    if (!$key['is_folder']) {
                        $this->mediaFileRepository->forceDelete(['id' => $key['id']]);
                    } else {
                        $this->mediaFolderRepository->deleteFolder($key['id'], true);
                    }
                }
                $response = "delete success";
                break;

                // case 'favorite':
                //     $meta = $this->mediaSettingRepository->firstOrCreate([
                //         'key' => 'favorites',
                //         'user_id' => Auth::id(),
                //     ]);

                //     if (!empty($meta->value)) {
                //         $meta->value = array_merge($meta->value, $request->input('selected', []));
                //     } else {
                //         $meta->value = $request->input('selected', []);
                //     }

                //     $this->mediaSettingRepository->createOrUpdate($meta);

                //     $response = RvMedia::responseSuccess([], trans('core/media::media.favorite_success'));

                //     break;

                // case 'remove_favorite':
                //     $meta = $this->mediaSettingRepository->firstOrCreate([
                //         'key' => 'favorites',
                //         'user_id' => Auth::id(),
                //     ]);

                //     if (!empty($meta)) {
                //         $value = $meta->value;
                //         if (!empty($value)) {
                //             foreach ($value as $key => $item) {
                //                 foreach ($request->input('selected') as $selectedItem) {
                //                     if ($item['is_folder'] == $selectedItem['is_folder'] && $item['id'] == $selectedItem['id']) {
                //                         unset($value[$key]);
                //                     }
                //                 }
                //             }
                //             $meta->value = $value;

                //             $this->mediaSettingRepository->createOrUpdate($meta);
                //         }
                //     }

                //     $response = RvMedia::responseSuccess([], trans('core/media::media.remove_favorite_success'));

                //     break;

                // case 'rename':
                //     foreach ($actionDto->listIds as $id) {
                //         if (!$item['id'] || !$item['name']) {
                //             continue;
                //         }

                //         $id = $item['id'];
                //         if ($item['is_folder'] == 'false') {
                //             $file = $this->fileRepository->getFirstBy(['id' => $id]);

                //             if (!empty($file)) {
                //                 $file->name = $this->fileRepository->createName($item['name'], $file->folder_id);
                //                 $this->fileRepository->createOrUpdate($file);
                //             }
                //         } else {
                //             $name = $item['name'];
                //             $folder = $this->folderRepository->getFirstBy(['id' => $id]);

                //             if (!empty($folder)) {
                //                 $folder->name = $this->folderRepository->createName($name, $folder->parent_id);
                //                 $this->folderRepository->createOrUpdate($folder);
                //             }
                //         }
                //     }

                //     $response = RvMedia::responseSuccess([], trans('core/media::media.rename_success'));

                //     break;

                // case 'empty_trash':
                //     $this->folderRepository->emptyTrash();
                //     $this->fileRepository->emptyTrash();

                //     $response = RvMedia::responseSuccess([], trans('core/media::media.empty_trash_success'));

                //     break;
        }

        return $response;
    }
}