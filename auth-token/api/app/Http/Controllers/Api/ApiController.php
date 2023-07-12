<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    /**
     * Return a success JSON success response.
     */
    public static function successResponse($data = [], $httpCode = 200) {
        return response()->json($data, intval($httpCode), ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Return a formatted JSON error response
     */
    public static function errorResponse($message = null, $httpCode = 400) {
        $response = array(
            'success' => false,
            'statusCode' => $httpCode,
            'message'    => $message,
        );
        return response()->json($response, intval($httpCode), ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

    }
}
