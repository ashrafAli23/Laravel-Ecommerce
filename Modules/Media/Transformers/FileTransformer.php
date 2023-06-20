<?php

declare(strict_types=1);

namespace Modules\Media\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;
use Modules\Media\Facades\MediaFacade;

class FileTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'basename' => File::basename($this->url),
            'full_url' => MediaFacade::url($this->url),
            'mime_type' => $this->mime_type,
            'options' => $this->options,
            'folder_id' => $this->folder_id,
            'size' => MediaFacade::humanFilesize($this->size),
            'thumb' => $this->canGenerateThumbnails() ? MediaFacade::getImageUrl($this->url, 'thumb') : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}