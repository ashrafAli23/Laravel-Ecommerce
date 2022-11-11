<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as STATUS;

trait Response
{
    /**
     * handle Error response
     * @param {$errNum: Number, $errMSG: String}
     * @return JsonResponse {status , message}
     */
    public function errorResponse($errMSG, $errNum): JsonResponse
    {
        $code = $errNum ? $errNum : STATUS::HTTP_BAD_REQUEST;
        return response()->json([
            "status" => false,
            "message" => $errMSG
        ], $code);
    }

    /**
     * handle Error response
     * @param Number $errNum  String $massege
     * @return JsonResponse {status , message}
     */
    public function successResponse($massege, $errNum): JsonResponse
    {
        return response()->json([
            "status" => true,
            "message" => $massege
        ], $errNum);
    }

    /**
     * handle Error response
     * @param {$errNum: Number,$data: Array}
     * @return JsonResponse {data }
     */
    public function dataResponse($data, $errNum): JsonResponse
    {
        return response()->json($data, $errNum);
    }
}
