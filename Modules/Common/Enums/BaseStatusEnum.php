<?php

declare(strict_types=1);

namespace Modules\Common\Enums;

class BaseStatusEnum
{
    public const PUBLISHED = 'published';
    public const DRAFT = 'draft';
    public const PENDING = 'pending';

    public static function toArray(): array
    {
        return [
            static::DRAFT,
            static::PENDING,
            static::PUBLISHED
        ];
    }
}