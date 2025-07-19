<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CheckRevokedToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    $token = $request->user()->token();

    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $tokenId = $token->id;

    if (Redis::get("blacklist:token:$tokenId")) {
        return response()->json(['message' => 'Token has been revoked'], 401);
    }

    return $next($request);
}

}
