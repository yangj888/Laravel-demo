<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    // 从请求头中获取 AWS Trace ID
    $traceId = request()->header('X-Amzn-Trace-Id', 'N/A');

    // 获取关键环境变量
    $appConfig = getAppConfig();
    $env = env('APP_ENV', 'local');
    $appName = $appConfig['APP_NAME'] ?? env('APP_NAME', 'LaravelApp');
    $version = env('APP_VERSION', 'v1.0.0');

    // 记录访问日志（JSON 格式）
    Log::info('Homepage accessed', [
        'trace_id' => $traceId,
        'env' => $env,
        'version' => $version,
        'client_ip' => request()->ip(),
        'user_agent' => request()->header('User-Agent'),
        'timestamp' => now()->toISOString(),
    ]);

    // 返回 JSON 响应
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
        // 从 AppConfig Agent 获取配置
        $url = 'http://localhost:2772/applications/laravel-config-v1/environments/laravel-appconfig-demo/configurations/laravel-config';
        
        // 使用 Guzzle 或 HTTP 客户端请求 AppConfig Agent
        $response = Http::get($url);

        // 如果请求成功，返回配置数据
        if ($response->successful()) {
            return $response->json();  // 假设返回 JSON 格式
        }

        // 如果失败，记录错误并返回默认配置
        Log::error('Failed to fetch configuration from AppConfig Agent');
        return [];
    } catch (\Exception $e) {
        // 记录异常并返回默认配置
        Log::error('Error fetching configuration: ' . $e->getMessage());
        return [];
    }
}
