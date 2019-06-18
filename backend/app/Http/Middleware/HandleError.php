<?php

namespace App\Http\Middleware;

use App\PermissionException;
use Closure;
use Illuminate\Auth\AuthenticationException;

class HandleError
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        \Log::debug("Request", ['m'=>$request->method(), 'uri'=>$request->url(), 'p' => $request->all()]);
        $response = $next($request);
        $exception = $response->exception;
        if ($exception instanceof PermissionException) {
            return response()->json(['message'=>$exception->getMessage()], 403);
        } else if ($exception instanceof AuthenticationException) {
            return response()->json(['message'=>$exception->getMessage()], 401);
        } else if ($exception instanceof \Exception) {
            return response()->json(['message'=>$exception->getMessage()], 400);
        }

        return $response;
    }
}
