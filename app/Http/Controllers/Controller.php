<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * @OA\Info(title="My API", version="1.0")
 */
abstract class Controller
{
    public function successResponse($data, $message = 'Request successful', $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
    
    public function failureResponse($message, $statusCode = Response::HTTP_BAD_REQUEST, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
