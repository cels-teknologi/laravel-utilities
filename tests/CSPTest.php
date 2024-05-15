<?php

namespace Cels\Utilities\Tests;

use Cels\Utilities\CSP\CSP;
use Cels\Utilities\CSP\Middleware\AddCSPHeaders;

class CSPTest extends TestCase
{
    /**
     * A basic test to ensure Content-Security-Policy header is set.
     *
     * @return void
     */
    public function test_that_csp_header_exists()
    {
        CSP::$enabled = true;

        app('router')
            ->middleware([AddCSPHeaders::class])
            ->get('/', fn () => 'Hello, world!');

        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');

        CSP::$enabled = false;
    }
}