<?php

namespace Cels\Utilities\CSP;

use Cels\Utilities\CSP\Policies\Basic;

class CSP
{
    public const SINGLETON_KEY = 'cels-utilities_csp-nonce';

    public static bool $enabled = false;

    public static $policy = Basic::class;

    public function nonce()
    {
        return \bin2hex(\random_bytes(32));
    }
}