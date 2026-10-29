<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Helper to call internal API using HTTP Client with JWT Token of currently logged in user.
     */
    protected function apiCall($method, $endpoint, $data = [])
    {
        $http = \Illuminate\Support\Facades\Http::withHeaders([
            'Accept' => 'application/json',
        ]);

        if (auth()->check()) {
            try {
                // Generate a JWT token for the currently authenticated web user
                $token = auth('api')->login(auth()->user());
                if ($token) {
                    $http = $http->withToken($token);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('JWT token generation in Controller failed: ' . $e->getMessage());
            }
        }

        $base = env('INTERNAL_API_URL') ?: url('/');
        $url = rtrim($base, '/') . '/api/' . ltrim($endpoint, '/');
        
        $response = $method === 'GET' 
            ? $http->get($url, $data) 
            : $http->$method($url, $data);

        return $response;
    }

    protected function paginateApiResponse($responseData, $request)
    {
        $rawItems = $responseData['data']['data'] ?? $responseData['data'] ?? [];
        $items = is_array($rawItems) ? json_decode(json_encode($rawItems)) : [];
        
        $total = $responseData['data']['total'] ?? count($items);
        $perPage = $responseData['data']['per_page'] ?? 10;
        $currentPage = $responseData['data']['current_page'] ?? 1;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => url($request->path()), 'query' => $request->query()]
        );
    }
}
