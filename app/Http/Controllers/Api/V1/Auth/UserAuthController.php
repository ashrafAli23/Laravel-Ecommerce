<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UserRequest;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as STATUS;

class UserAuthController extends Controller
{
    use Response;

    public function register(UserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken("userAuthToken", ['user-access'])->plainTextToken;
            DB::commit();

            return $this->dataResponse(['user' => $user, 'token' => $token], STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_FORBIDDEN);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = $request->user()
                ->createToken("userAuthToken", ['user-access'])
                ->plainTextToken;

            return $this->dataResponse(['token' => $token], STATUS::HTTP_OK);
        }

        return $this->errorResponse('Invalid email or password', STATUS::HTTP_UNAUTHORIZED);
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse("logout", STATUS::HTTP_OK);
    }
}
