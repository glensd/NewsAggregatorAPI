<?php

if (!function_exists('ApiResponse')) {
    function ApiResponse($status, $message, $data, $responseCode)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $responseCode);
    }
}

