<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Repository\DashboardRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as STATUS;

class AccountController extends Controller
{
    use Response;
    private DashboardRepository $order;

    public function __construct(DashboardRepository $order)
    {
        $this->order = $order;
    }
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $data = [
                'totalOrder' => $this->order
                    ->getTotalOfSomeData(new Order(), ['user_id' => $user->id]),
                'pendingOrder' => $this->order
                    ->getTotalOfSomeData(new Order(), ['user_id' => $user->id, 'status' => 'pending']),
                'completedOrder' => $this->order
                    ->getTotalOfSomeData(new Order(), ['user_id' => $user->id, 'status' => 'completed']),
                'cancelledOrder' => $this->order
                    ->getTotalOfSomeData(new Order(), ['user_id' => $user->id, 'status' => 'cancelled']),
                'refundedOrder' => $this->order
                    ->getTotalOfSomeData(new Order(), ['user_id' => $user->id, 'status' => 'refunded']),

            ];

            return $this->dataResponse($data, STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function getProfile(): JsonResponse
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            return $this->dataResponse([], STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'nullable|string|min:3',
            'last_name' => 'nullable|string|min:3',
            'password' => 'nullable|min:6'
        ]);

        try {
            $user = Auth::user();
            $userData = User::findOr($user->id, function () {
                return "User not found";
            });

            if ($request->password) {
                $userData->passowrd = Hash::make($request->password);
            }

            if ($request->first_name || $request->last_name) {
                $request->first_name ?
                    $userData->first_name = $request->first_name : null;

                $request->last_name ?
                    $userData->last_name = $request->last_name : null;
            }
            return $this->successResponse("Profile Updated Successfully.", STATUS::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function deleteAccount(): JsonResponse
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $user->delete();
            return $this->successResponse("Account deleted Successfully.", STATUS::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
