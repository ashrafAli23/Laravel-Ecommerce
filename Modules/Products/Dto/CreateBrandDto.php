<?php

namespace Modules\Products\Dto;

use InvalidArgumentException;
use Modules\Products\Http\Requests\V1\BrandRequest;

class CreateBrandDto
{
    public readonly string $name;
    public readonly string $slug;
    public readonly ?string $description;
    public readonly string $status;
    public readonly int $order;
    public readonly int $is_featured;
    public readonly array $meta;

    public function __construct(
        string $name,
        string $slug,
        string $description = null,
        string $status = 'pending',
        int $order = 0,
        bool $is_featured = false,
        array $meta
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->status = $status;
        $this->order = $order;
        $this->is_featured = $is_featured;
        $this->meta = $meta;
        $this->validate();
    }

    public static function create(BrandRequest $request): self
    {
        return new self(
            $request->name,
            $request->slug,
            $request->description ?? null,
            $request->status,
            $request->order,
            $request->is_featured ?? false,
            $request->seo_meta ?? ''
        );
    }

    /**
     * Validation method
     *
     * @return void
     */
    private function validate(): void
    {
        if (!isset($this->name)) {
            throw new InvalidArgumentException("Name field is required", 400);
        }
    }
}
