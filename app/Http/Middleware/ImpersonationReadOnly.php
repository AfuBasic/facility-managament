<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationReadOnly
{
    /**
     * Handle an incoming request.
     * Blocks all write operations when in impersonation mode.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('impersonating') && $this->isWriteRequest($request)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Write operations are disabled in impersonation mode.',
                ], 403);
            }

            return back()->with('error', 'Write operations are disabled in impersonation mode.');
        }

        return $next($request);
    }

    protected function isWriteRequest(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }
}
