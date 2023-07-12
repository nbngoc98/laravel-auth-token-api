<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        Log::warning(
            Route::currentRouteAction() . ":\n" . json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        throw new HttpResponseException(response()->json([
            'success' => false,
            'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message'    => $validator->errors()->toArray(),
            'messageCode'    => null,
        ], Response::HTTP_UNPROCESSABLE_ENTITY,  ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE));
    }
}
