<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // PERBAIKI: Redirect berdasarkan path/guard
        if ($request->is('siswa/*') || $request->is('siswa')) {
            return route('siswa.login');
        }

        // Default untuk admin
        return route('backend.login');
    }
}