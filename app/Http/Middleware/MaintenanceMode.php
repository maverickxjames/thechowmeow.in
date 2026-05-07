<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $isEnabled = Setting::where('key', 'maintenance_mode')->value('value') === '1';

        if (!$isEnabled) {
            return $next($request);
        }

        // Let admins through — they can still browse the frontend
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // Let auth routes through so admin can still log in
        if ($request->is('login', 'logout', 'register', 'password/*')) {
            return $next($request);
        }

        $message = Setting::where('key', 'maintenance_message')->value('value')
            ?? 'We\'re currently performing scheduled maintenance. We\'ll be back shortly!';

        return response()->view('maintenance', [
            'message' => $message,
        ], 503);
    }
}
