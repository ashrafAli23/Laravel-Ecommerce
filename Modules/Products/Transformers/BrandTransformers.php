<?php

namespace Modules\Products\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandTransformers extends JsonResource
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
            'description' => $this->description,
            'logo' => $this->logo,
            'status' => $this->status
        ];
    }
}
