<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogUserAccess
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
        $ip = $request->ip();
        $time = now();
        $message = 'Phát hiện có người vào website';
return $next($request);
        Log::channel('telegram')
            ->info( $message,
                [
                    'Địa chỉ IP' => $ip,
                    'THời gian' => $time->format('H:i:s d/m/Y'),
                    'Domain' => $request->getHost(),
                    'Url' => $request->url(),
                ]
            );

        return $next($request);
    }
}
