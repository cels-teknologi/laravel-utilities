<?php

namespace Cels\Utilities\CSP\Middleware;

use Cels\Utilities\CSP\CSP;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
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
        if (CSP::$enabled) {
            CSP::nonce();
        }

        $response = $next($request);

        if (CSP::$enabled) {
            $result = app(CSP::$policy)->build(CSP::getSharedNonce());
            if ($result) {
                $response->headers->add([
                    'Content-Security-Policy' => (string) $result,
                ]);
            }
        }

        return $response;
    }
}
