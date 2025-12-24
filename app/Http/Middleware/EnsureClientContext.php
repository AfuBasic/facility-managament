<?php

namespace App\Http\Middleware;

use App\Services\CurrentClientResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientContext
{

    /**
     * Inject the Client Resolver in to determine if we have a valid client in place
     */
    public function __construct(private CurrentClientResolver $clientResolver){}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = $this->clientResolver->resolve($request);
        if(!$client) {
            abort(403);
        }
        app()->instance('currentClient', $client);
        return $next($request);
    }
}
