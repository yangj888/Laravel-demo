<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $traceId = request()->header('X-Amzn-Trace-Id', 'N/A');

    $appConfig = getAppConfig();
    $env = env('APP_ENV', 'local');
    $appName = $appConfig['APP_NAME'] ?? env('APP_NAME', 'LaravelApp');
    $version = env('APP_VERSION', 'v1.0.0');

    Log::info('Homepage accessed', [
        'trace_id' => $traceId,
        'env' => $env,
        'version' => $version,
        'client_ip' => request()->ip(),
        'user_agent' => request()->header('User-Agent'),
        'timestamp' => now()->toISOString(),
    ]);

    return response()->json([
        'app' => $appName,
        'version' => $version,
        'environment' => $env,
        'trace_id' => $traceId,
        'timestamp' => now()->toISOString(),
    ]);
});

function getAppConfig()
{
    try {
        $url = 'http://localhost:2772/applications/laravel-config-v1/environments/laravel-appconfig-demo/configurations/laravel-config';
        
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Failed to fetch configuration from AppConfig Agent');
        return [];
    } catch (\Exception $e) {
        Log::error('Error fetching configuration: ' . $e->getMessage());
        return [];
    }
}
