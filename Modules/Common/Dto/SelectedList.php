<?php

declare(strict_types=1);

namespace Modules\Common\Dto;

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
    public function create(array $listIds): SelectedList
    {
        return new self($listIds);
    }
}
