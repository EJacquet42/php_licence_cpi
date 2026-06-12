<?php

namespace App\Http\Middleware;

use App\Models\Log;
use App\Services\RsyslogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogSensitiveAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $log = Log::create([
                'user_id' => $request->user()->id,
                'type' => 'access',
                'facility' => 'authpriv',
                'priority' => 'info',
                'message' => "Accès à {$request->method()} {$request->path()} — id {$request->user()->id}",
            ]);

            try {
                app(RsyslogService::class)->send($log);
            } catch (\Throwable) {
            }
        }

        return $response;
    }
}
