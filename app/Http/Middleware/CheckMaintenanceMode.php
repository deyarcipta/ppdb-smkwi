<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengaturan = \App\Models\PengaturanAplikasi::getSettings();
        
        if ($pengaturan->maintenance_mode && !$request->is('admin/*') && !auth()->check()) {
            return response()->view('maintenance', [
                'message' => $pengaturan->maintenance_message
            ], 503);
        }

        return $next($request);
    }
}