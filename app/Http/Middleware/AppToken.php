<?php

namespace App\Http\Middleware;

use Closure;

class AppToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = "RkJBUfQZSCNwnfZlJub4";

        if (!empty($request->header('appToken')) && $request->header('appToken') === $token){
            return $next($request);
        }
        $response = [
            'status' => 'error',
            'data' => [
                'error_code' => 0
            ]
        ];
        return response()->json($response, 400);
    }
}
