<?php

namespace Cels\Utilities\CSP\Middleware;

use Cels\Utilities\CSP\CSP;
use Cels\Utilities\Utility;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCSPHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (CSP::$enabled) {
            CSP::generateNonce();
            $response->headers->add([
                'Content-Security-Policy' => (string) app(CSP::$policy),
            ]);
        }

        return $response;
    }
}
