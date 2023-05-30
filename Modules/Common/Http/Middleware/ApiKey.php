<?php

namespace Modules\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        if (!isset($apiKey)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }
        return $next($request);
    }
}
