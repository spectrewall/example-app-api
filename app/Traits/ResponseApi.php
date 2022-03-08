<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseApi
{
    /**
     * Core of response
     *
     * @param object|array $message
     * @param null $data
     * @param integer $statusCode
     * @param boolean $isSuccess
     * @return JsonResponse
     */
    public function coreResponse($message, int $statusCode, $data = null, $isSuccess = true): JsonResponse
    {
        // Check the params
        if (!$message) return response()->json(['message' => 'Message is required'], 500);

        // Send the response
        if ($isSuccess) {
            return response()->json([
                'message' => $message,
                'data' => $data
            ], $statusCode);
        } else {
            return response()->json([
                'message' => $message,
            ], $statusCode);
        }
    }

    /**
     * Send any success response
     *
     * @param object|array $message
     * @param object|array $data
     * @param integer $statusCode
     * @return JsonResponse
     */
    public function success($message, $data = null, $statusCode = 200): JsonResponse
    {
        return $this->coreResponse($message, $statusCode, $data);
    }

    /**
     * Send any error response
     *
     * @param object|array $message
     * @param integer $statusCode
     * @return JsonResponse
     */
    public function error($message, $statusCode = 500): JsonResponse
    {
        return $this->coreResponse($message, $statusCode, null, false);
    }
}
