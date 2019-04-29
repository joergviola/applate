<?php

namespace App\Http\Middleware;

use App\PermissionException;
use Closure;

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
        $response = $next($request);
        $exception = $response->exception;
        if ($exception instanceof PermissionException) {
            return response()->json(['message'=>$exception->getMessage()], 403);
        } else if ($exception instanceof \Exception) {
            return response($exception->getMessage(), 400);
        }
        return $response;
    }
}