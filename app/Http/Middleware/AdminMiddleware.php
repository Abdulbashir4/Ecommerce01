<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isActive()) {
            abort(403, 'Admin access required.');
        }

        if (!$user->isSuperAdmin() && !$user->hasPermission('admin.access')) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
