<?php

namespace FreeradiusWeb\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response as LaravelResponse;
use Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data,
            $statusCode = LaravelResponse::HTTP_OK) {
            return Response::json([
                'error' => false,
                'data' => $data
            ], $statusCode);
        });

        Response::macro('error', function ($message,
            $statusCode = LaravelResponse::HTTP_BAD_REQUEST,
            $errorCode = 'UNKNOWN') {
            $decoded = json_decode($message, true);
            return Response::json([
                'error' => true,
                'code' => $errorCode,
                'message' => $decoded !== null ? $decoded : $message
            ], $statusCode);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
