<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use App\Repository\DashboardRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as STATUS;

class DashboardController extends Controller
{
    use Response;
    private DashboardRepository $account;

    public function __construct(DashboardRepository $account)
    {
        $this->account = $account;
    }

    public function index(): JsonResponse
    {
        try {
            $data =
                [
                    'totalOrders' => $this->account->getTotalModel(new Order()),
                    'totalUsers' => $this->account->getTotalModel(new User()),
                    'totalCategories' => $this->account->getTotalModel(new Category()),
                    'totalProducts' => $this->account->getTotalModel(new Product()),
                    'totalVariants' => $this->account->getTotalModel(new Variant()),
                    'totalPendingOrders' => $this->account->getTotalOfSomeData(new Order(), ['status' => 'pending']),
                    'totalCompletedOrders' => $this->account->getTotalOfSomeData(new Order(), ['status' => 'completed']),
                    'totalCancelledOrders' => $this->account->getTotalOfSomeData(new Order(), ['status' => 'cancelled']),
                    'totalRefundedOrders' => $this->account->getTotalOfSomeData(new Order(), ['status' => 'refunded']),
                ];


            return $this->dataResponse($data, STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
