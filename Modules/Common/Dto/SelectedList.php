<?php

declare(strict_types=1);

namespace Modules\Common\Dto;

use InvalidArgumentException;
use Modules\Common\Http\Requests\SelectedListRequest;

class SelectedList
{
    public readonly array $listIds;

    public function __construct(array $listIds)
    {
        $this->listIds = $listIds;
        $this->validate();
    }

    /**
     * @param array $listIds
     * @return SelectedList
     */
    public static function create(SelectedListRequest $request): SelectedList
    {
        return new self(json_decode($request->ids));
    }

    /**
     * Validation method
     *
     * @return void
     */
    private function validate(): void
    {
        if (empty($this->listIds)) {
            throw new InvalidArgumentException("Please select at least one record to perform this action!", 400);
        }
    }
}