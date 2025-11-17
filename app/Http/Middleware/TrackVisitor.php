<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Facades\Session;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Buat session ID jika belum ada
        if (!Session::has('visitor_session')) {
            Session::put('visitor_session', uniqid());
        }

        // Simpan data visitor
        Visitor::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_visited' => $request->path(),
            'session_id' => Session::get('visitor_session'),
            'visit_date' => now()->toDateString(),
        ]);

        return $next($request);
    }
}