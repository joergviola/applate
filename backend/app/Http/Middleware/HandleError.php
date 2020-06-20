<?php

namespace App\Http\Middleware;

use App\PermissionException;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;

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
        } else if ($exception instanceof QueryException) {
            if (is_array($exception->errorInfo) && count($exception->errorInfo)==3) {
                $data = [
                    'status' => $exception->errorInfo[0],
                    'code' => $exception->errorInfo[1],
                    'error' => $exception->errorInfo[2],
                    'message' => $exception->errorInfo[2],
                ];
                $this->userError($data, $request->method());
            } else {
                $data = [
                    'message'=>$exception->getMessage(),
                ];
            }
            return response()->json($data, 400);
        } else if ($exception instanceof \Exception) {
            return response()->json(['message'=>$exception->getMessage()], 400);
        }

        return $response;
    }

    private function userError(&$data, $action) {
        switch ($data['status']) {
            case '23000':
                switch ($data['code']) {
                    case 1451:
                        switch ($action) {
                            case 'PUT':
                                $data['message'] = 'Could not change - other data is dependent';
                                break;
                            case 'DELETE':
                                $data['message'] = 'Could not delete - used elsewhere or has dependent data';
                                break;
                            }
                        break;
                }
                break;
        }
    }
}
