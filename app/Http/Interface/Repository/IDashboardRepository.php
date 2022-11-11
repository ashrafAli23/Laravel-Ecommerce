<?php

declare(strict_types=1);

namespace App\Http\Interface\Repository;

use Illuminate\Database\Eloquent\Model;

interface IDashboardRepository
{
    public function getTotalModel(Model $model): int;
    public function getTotalOfSomeData(Model $model, array $query): int;
}
