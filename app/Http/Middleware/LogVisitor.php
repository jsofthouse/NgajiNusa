<?php
// app/Http/Middleware/LogVisitor.php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LogVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Skip logging untuk request admin & asset biar gak nyampah
        if (!$request->is('admin/*') && $request->method() === 'GET') {
            VisitorLog::updateOrInsert(
                [
                    'visit_date'  => now()->toDateString(),
                    'path'        => $request->path(),
                    'ip_address'  => $request->ip(),
                ],
                [
                    'hit_count'   => DB::raw('hit_count + 1'),
                    'updated_at'  => now(),
                ]
            );
        }

        return $next($request);
    }
}
