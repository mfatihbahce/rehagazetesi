<?php

namespace App\Http\Middleware;

use App\Models\VisitLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldLog($request)) {
            try {
                VisitLog::log(
                    $request->path(),
                    $request->ip() ?? '0.0.0.0',
                    $request->userAgent(),
                    $request->header('referer')
                );
            } catch (\Throwable) {
                // sessiz başarısız - log'lama hatası uygulamayı bozmasın
            }
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        if ($request->is('admin*') || $request->is('api*') || $request->is('_debugbar*')) {
            return false;
        }
        if (in_array(strtolower($request->method()), ['post', 'put', 'patch', 'delete'])) {
            return false;
        }
        $ext = pathinfo($request->path(), PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'woff', 'woff2', 'svg'])) {
            return false;
        }
        return true;
    }
}
