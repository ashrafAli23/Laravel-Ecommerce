<?php

declare(strict_types=1);

namespace Modules\Common\Dto;

use Modules\Common\Http\Requests\SelectedListRequest;

class SelectedList
{
    public readonly array $listIds;

    public function __construct(array $listIds)
    {
        $this->listIds = $listIds;
    }

    /**
     * @param array $listIds
     * @return SelectedList
     */
    public static function create(SelectedListRequest $request): SelectedList
    {
        return new self(json_decode($request->ids));
    }
}
