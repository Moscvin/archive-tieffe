<?php

namespace App\Http\Middleware;

use Closure;
use App\CoreUsers;

class UserToken
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
        if (!empty($request->header('userToken'))) {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
            if($user) {
                return $next($request);
            }
        }
        $response = [
            'status' => 'error',
            'data' => [
                'error_code' => 1
            ]
        ];
        return response()->json($response, 400);
    }
}
