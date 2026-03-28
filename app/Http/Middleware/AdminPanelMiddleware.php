<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPanelMiddleware
{
    /**
     * Admin paneline sadece admin ve editör erişebilir.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
        }

        if (!in_array($request->user()->role, ['admin', 'editor'])) {
            abort(403);
        }

        return $next($request);
    }
}
