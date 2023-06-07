<?php

declare(strict_types=1);

namespace Modules\Media\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class FolderTransformer extends JsonResource
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
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at
        ];
    }
}