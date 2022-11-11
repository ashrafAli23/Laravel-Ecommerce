<?php

declare(strict_types=1);

namespace App\Repository;

use App\Http\Interface\Repository\IDashboardRepository;
use Illuminate\Database\Eloquent\Model;

class DashboardRepository implements IDashboardRepository
{
    public function getTotalModel(Model $model): int
    {
        $data = $model->count();
        return $data;
    }

    public function getTotalOfSomeData(Model $model, array $query): int
    {
        $data = $model->where($query)->count();
        return $data;
    }
}
