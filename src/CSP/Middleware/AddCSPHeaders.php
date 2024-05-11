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
        CSP::generateNonce();
        $response = $next($request);
        if (CSP::$enabled) {
            $result = (string) app(CSP::$policy)->build();
            if ($result) {
                $response->headers->add([
                    'Content-Security-Policy' => $result,
                ]);
            }
        }

        return $response;
    }
}
