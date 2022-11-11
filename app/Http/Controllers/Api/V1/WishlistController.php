<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as STATUS;

class WishlistController extends Controller
{
    use Response;

    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $wishlist = Wishlist::with(['variant'])
                ->where('user_id', $user->id)->get();

            if (!$wishlist->count()) {
                return $this->errorResponse("Wishlist not found", STATUS::HTTP_NOT_FOUND);
            }

            return $this->dataResponse(WishlistResource::collection($wishlist), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // validate request
            $request->validate([
                'variant_id' => 'required|numeric'
            ]);

            // check variant if already exists
            $checkWishlist = Wishlist::where([
                'user_id' => $user->id,
                'variant_id' => $request->variant_id
            ])->first();

            if (isset($checkWishlist)) {
                return $this
                    ->errorResponse('Already Added To Wishlist.', STATUS::HTTP_BAD_REQUEST);
            }

            // add product to wishlist
            $user->wishlist()->create([
                'variant_id' => $request->variant_id
            ]);

            return $this
                ->successResponse('Successfully Added To The Wishlist.', STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $wishlist = Wishlist::find((int)$id);

            if (!isset($wishlist) || $wishlist->user_id !== $user->id) {
                return $this->errorResponse("Wishlist not found", STATUS::HTTP_NOT_FOUND);
            }

            $wishlist->delete();

            return $this->successResponse("Deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function destroyAll()
    {
        try {
            $user = Auth::user();

            $checkWishlist = Wishlist::where('user_id', $user->id)->first();

            if (!isset($checkWishlist)) {
                return $this->errorResponse("Wishlist not found", STATUS::HTTP_NOT_FOUND);
            }

            $user->wishlist()->delete();

            return $this->successResponse("Wishlist cleared successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
