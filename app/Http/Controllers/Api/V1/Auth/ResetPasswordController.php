<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response as STATUS;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use Response;

    public function reset_password_request(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->errorResponse("Your email not found", STATUS::HTTP_NOT_FOUND);
            }

            $token = Str::random(10);
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]);

            Mail::to($user->email)->send(new PasswordReset($token));
            DB::commit();

            return $this->successResponse("Check your email", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function verify_token(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse("Your email not found", STATUS::HTTP_NOT_FOUND);
        }

        $data = DB::table('password_resets')
            ->where(['email' => $user->email])
            ->first();

        if (!$data || $data->token !== $request->token) {
            return $this->errorResponse("Invalid token", STATUS::HTTP_BAD_REQUEST);
        }

        return $this->successResponse("Vaild token", STATUS::HTTP_OK);
    }

    public function submit_new_password(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse("Your email not found", STATUS::HTTP_NOT_FOUND);
        }

        $data = DB::table('password_resets')
            ->where(['email' => $user->email])
            ->first();

        if (!$data || $data->token !== $request->token) {
            return $this->errorResponse("Invalid token", STATUS::HTTP_BAD_REQUEST);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        $data->delete();

        return $this->successResponse("Password reset successfully", STATUS::HTTP_OK);
    }
}
