<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    // 从请求头中获取 AWS Trace ID
    $traceId = request()->header('X-Amzn-Trace-Id', 'N/A');

    // 获取关键环境变量
    $env = env('APP_ENV', 'local');
    $appName = env('APP_NAME', 'LaravelApp');
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
