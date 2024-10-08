<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAcquiringIPsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $acquiring = config('payment.default');
        $allowedIps = config("payment.{$acquiring}.allowed_ips");

        if (empty($allowedIps)) {
            return $next($request);
        }

        if (in_array($request->ip(), $allowedIps)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
