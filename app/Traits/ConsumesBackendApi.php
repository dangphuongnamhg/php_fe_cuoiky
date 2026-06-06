<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

trait ConsumesBackendApi
{
    /**
     * Get the Http client pointing to the backend API.
     * It automatically attaches the Authorization Bearer token if the user is authenticated.
     */
    protected function api(): PendingRequest
    {
        $baseUrl = config('services.backend.url');
        $client = Http::baseUrl($baseUrl)
            ->acceptJson();

        $token = session('api_token');
        if ($token) {
            $client->withToken($token);
        }

        return $client;
    }
}
