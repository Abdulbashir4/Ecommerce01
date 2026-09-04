<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $timezone = Setting::get('general.timezone', config('app.timezone', 'UTC'));
        if (is_string($timezone) && in_array($timezone, timezone_identifiers_list(), true)) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        $maintenance = (bool) Setting::get('general.maintenance', false);

        // Keep authentication and admin access available so an administrator can
        // turn maintenance mode off again.
        if ($maintenance
            && !$request->is('admin', 'admin/*')
            && !$request->is('login')
            && !$request->is('logout')
        ) {
            return response()->view('maintenance', [
                'message' => Setting::get(
                    'general.maintenance_message',
                    'We are currently performing scheduled maintenance. Please check back soon.'
                ),
                'siteName' => Setting::get('general.site_name', 'Optimum Biomedical'),
            ], 503);
        }

        return $next($request);
    }
}
