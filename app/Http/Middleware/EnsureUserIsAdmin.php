<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //Check if user not admin denied
        if($request->user()?->role !== 'admin') {
            return response()->json(['message' => "Akses ditolak"],403);
        }
        return $next($request);
    }
}
