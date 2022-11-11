<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => 'product',
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'current_stock' => $this->current_stock,
            'images' => $this->images,
            'price' => $this->price,
            'retail' => $this->retail,
            'active' => $this->active,
            'vat' => $this->vat,
            'category' => new CategoryResource(
                $this->whenLoaded(
                    'category',
                )
            ),
            'variant' => VariantResource::collection(
                $this->whenLoaded(
                    'variant',
                )
            ),
            'brand' => VariantResource::collection(
                $this->whenLoaded(
                    'brand',
                )
            )
        ];
    }
}
