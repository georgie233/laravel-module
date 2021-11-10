<?php

namespace App\Http\Middleware;

use Closure;

class AcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $res = $next($request);

        if ($request->header('Accept') && strstr($request->header('Accept'), 'application/json') > -1) {
            return $res;
        } else {
            return response()->json([
                'code' => 403,
                'message' => 'accept must be application/json',
            ]);
        }
    }
}
