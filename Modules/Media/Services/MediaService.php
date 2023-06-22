<?php

declare(strict_types=1);

namespace Modules\Media\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Dto\ActionDto;
use Modules\Media\Facades\MediaFacade;
use Modules\Media\Repositories\Interfaces\IMediaFileRepository;
use Modules\Media\Repositories\Interfaces\IMediaFolderRepository;
use Modules\Media\Utils\Zip\Zip;

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
            case 'restore':
                foreach ($actionDto->listIds as $key) {
                    $id = $key['id'];
                    if (!$key['is_folder']) {
                        $this->mediaFileRepository->restoreBy(['id' => $id]);
                    } else {
                        $this->mediaFolderRepository->restoreFolder($id);
                    }
                }
                $response = "restore success";
                break;

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

            case 'rename':
                foreach ($actionDto->listIds as $key) {
                    if (!$key['id'] || !$key['value']) {
                        continue;
                    }
                    if (!$key['is_folder']) {
                        $file = $this->mediaFileRepository->getFirstBy(['id' => $key['id']]);

                        if (!empty($file)) {
                            $file->name = $this->mediaFileRepository->createName($key['value'], $file->folder_id);
                            $this->mediaFileRepository->createOrUpdate($file);
                        }
                    } else {
                        $folder = $this->mediaFolderRepository->getFirstBy(['id' => $key['id']]);

                        if (!empty($folder)) {
                            $folder->name = $this->mediaFolderRepository->createName($key['value'], $folder->parent_id);
                            $this->mediaFolderRepository->createOrUpdate($folder);
                        }
                    }
                }

                $response = "rename success";
                break;
            case 'empty_trash':
                $this->mediaFolderRepository->emptyTrash();
                $this->mediaFileRepository->emptyTrash();

                $response = "empty trash success";

                break;
        }

        return $response;
    }

    public function download(ActionDto $actionDto)
    {
        $items = $actionDto->listIds;
        if (count($items) == 1 && !$items['0']['is_folder']) {
            $file = $this->mediaFileRepository->getFirstByWithTrash(['id' => $items[0]['id']]);
            if (!empty($file) && $file->type != 'video') {
                $filePath = MediaFacade::getRealPath($file->url);
                if (!MediaFacade::isUsingCloud()) {
                    if (!File::exists($filePath)) {
                        return throw new Exception("File not exists", 404);
                    }

                    return response()->download($filePath);
                }

                return response()->make(file_get_contents(str_replace('https://', 'http://', $filePath)), 200, [
                    'Content-type' => $file->mime_type,
                    'Content-Disposition' => 'attachment; filename="' . $file->name . '.' . File::extension($file->url) . '"',
                ]);
            }
        } else {
            $fileName = MediaFacade::getRealPath('download-' . Carbon::now()->format('Y-m-d-h-i-s') . '.zip');
            $zip = new Zip();
            $zip->make($fileName);
            foreach ($items as $item) {
                $id = $item['id'];
                if (!$item['is_folder']) {
                    $file = $this->mediaFileRepository->getFirstByWithTrash(['id' => $id]);
                    if (!empty($file) && $file->type != 'video') {
                        $filePath = MediaFacade::getRealPath($file->url);
                        if (!MediaFacade::isUsingCloud()) {
                            if (File::exists($filePath)) {
                                $zip->add($filePath);
                            }
                        } else {
                            $zip->addString(
                                File::basename($file),
                                file_get_contents(str_replace('https://', 'http://', $filePath))
                            );
                        }
                    }
                } else {
                    $folder = $this->mediaFolderRepository->getFirstByWithTrash(['id' => $id]);
                    if (!empty($folder)) {
                        if (!MediaFacade::isUsingCloud()) {
                            $zip->add(MediaFacade::getRealPath($this->mediaFolderRepository->getFullPath($folder->id)));
                        } else {
                            $allFiles = Storage::allFiles($this->mediaFolderRepository->getFullPath($folder->id));
                            foreach ($allFiles as $file) {
                                $zip->addString(
                                    File::basename($file),
                                    file_get_contents(str_replace('https://', 'http://', MediaFacade::getRealPath($file)))
                                );
                            }
                        }
                    }
                }
            }

            if (version_compare(phpversion(), '8.1') >= 0) {
                $zip = null;
            } else {
                $zip->close();
            }
            if (File::exists($fileName)) {
                return response()->download($fileName)->deleteFileAfterSend();
            }

            throw new Exception("download file error", 500);
        }

        throw new Exception("Can not download file", 500);
    }

    public function getList()
    {
    }
}