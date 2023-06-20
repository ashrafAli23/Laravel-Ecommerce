<?php

declare(strict_types=1);

namespace Modules\Media\Dto;

class ActionDto
{
    public function __construct(
        public readonly array $listIds,
        public readonly string $action,
    ) {
    }

    public static function create(
        array $listIds,
        string $action,
    ): self {
        return new self(
            listIds: $listIds,
            action: $action,
        );
    }
}