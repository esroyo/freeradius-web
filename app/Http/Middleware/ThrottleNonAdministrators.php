<?php

namespace FreeradiusWeb\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Routing\Middleware\ThrottleRequests;

class ThrottleNonAdministrators extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttemps = 60, $decayMinutes = 1)
    {
        $user = Auth::guard('api')->user();
        if ($user && $user->isAdministrator()) {
            // don't throttle at all
            return $next($request);
        } else {
            return parent::handle($request, $next, $maxAttemps, $decayMinutes);
        }
    }
}
