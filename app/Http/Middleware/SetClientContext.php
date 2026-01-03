<?php

namespace App\Http\Middleware;

use App\Services\CurrentClientResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetClientContext
{
    protected $resolver;

    public function __construct(CurrentClientResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = $this->resolver->resolve($request);

        if (!$client) {
            return redirect()->route('user.home')->with('error', 'Please select a client to continue.');
        }

        // Bind the current client to the container
        app()->instance('currentClient', $client);

        return $next($request);
    }
}
