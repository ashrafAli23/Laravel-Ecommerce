<?php

namespace Modules\Products\Dto;

use Modules\Products\Http\Requests\V1\BrandRequest;

class CreateBrandDto
{
    public readonly string $name;
    public readonly string $description;
    public readonly string $status;
    public readonly int $order;
    public readonly int $is_feature;

    public function __construct(
        string $name,
        string $description = null,
        string $status = 'pending',
        int $order = 0,
        int $is_feature = false
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->order = $order;
        $this->is_feature = $is_feature;
    }

    public static function create(BrandRequest $request): self
    {
        return new self(
            $request->name,
            $request->description ?? null,
            $request->status,
            $request->order,
            $request->is_feature ?? false,
        );
    }
}
