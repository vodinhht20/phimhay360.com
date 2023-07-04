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
        $message = 'Phát hiện có người vào website | IP:  {ip} | Time: {time}'

        Log::channel('ophim-view-web')
            ->info( $message,
                [
                    'ip' => $ip,
                    'time' => $time->format('H:i:s d/m/Y')
                ]
            );

        return $next($request);
    }
}
