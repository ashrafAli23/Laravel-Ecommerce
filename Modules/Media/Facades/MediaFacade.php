<?php

declare(strict_types=1);

namespace Modules\Media\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Media\Utils\Media;

class MediaFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Media::class;
    }
}
