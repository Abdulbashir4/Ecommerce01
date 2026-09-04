<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $user = $request->user();

        if (!$user || !$user->isActive()) {
            abort(403, 'Your account is inactive.');
        }

        if (!$user->hasAnyPermission($permissions)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
