<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->id,
            'usage_limit' => $this->usage_limit,
            'percentage_discount' => $this->percentage_discount,
            'expire_at' => $this->expire_at,
            'status' => $this->active,
        ];
    }
}
