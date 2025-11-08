<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RequestLogger
{
    public function handle($request, Closure $next)
    {
        Log::info('Incoming Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_id' => optional($request->user())->id,
            'body' => $request->except(['password', 'token']),
        ]);

        $response = $next($request);

        Log::info('Outgoing Response', [
            'status' => $response->status(),
            'url' => $request->fullUrl(),
        ]);

        return $response;
    }
}
