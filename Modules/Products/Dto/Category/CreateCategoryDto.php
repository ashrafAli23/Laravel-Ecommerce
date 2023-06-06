<?php

declare(strict_types=1);

namespace Modules\Products\Dto\Category;

use Modules\Products\Http\Requests\V1\CategoryRequest;

class CreateCategoryDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $description,
        public readonly ?int $parent_id,
        public readonly int $order,
        public readonly bool $is_featured,
        public readonly string $status,
        public readonly ?string $meta,
    ) {
    }

    public static function create(CategoryRequest $request): self
    {
        return new self(
            name: $request->name,
            slug: $request->slug,
            description: $request->description,
            parent_id: $request->parent_id ?? null,
            order: $request->order,
            is_featured: $request->is_featured ?? false,
            status: $request->status,
            meta: $request->seo_meta ?? null,
        );
    }
}
