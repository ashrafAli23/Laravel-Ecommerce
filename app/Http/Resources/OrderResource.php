<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'number' => $this->number,
            'paymant_method' => $this->paymant_method,
            'sub_total' => $this->sub_total,
            'coupon' => $this->coupon,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'address' => new OrderAddressResource(
                $this->whenLoaded(
                    'order_address',
                )
            ),
            'completed_at' => $this->completed_at,
            'cancelled_at' => $this->cancelled_at
        ];
    }
}
