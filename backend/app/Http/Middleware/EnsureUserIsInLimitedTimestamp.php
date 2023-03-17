<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Repositories\QueryBuilder\TimezoneMapper;

class EnsureUserIsInLimitedTimestamp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // $authController = new AuthController();
        // $user = $request->user();
        // $latitude = $request->header('latitude');
        // $longitude = $request->header('longitude');
        // $userTimeZone = TimezoneMapper::latLngToTimezoneString($latitude, $longitude);
        // if ($authController->isUserTimezoneInLimitedRange($user, $userTimeZone)) {
        //     return $next($request);
        // }
        // $user->token()->revoke();
        // throw new \Illuminate\Validation\UnauthorizedException(
        //     "you are not allowed to request at this moment!",
        //     401
        // );
        return $next($request);
    }
}
