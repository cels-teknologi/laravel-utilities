<?php

namespace Cels\Utilities\CSP;

use Cels\Utilities\CSP\Policies\Basic;

class CSP
{
    public static bool $enabled = false;

    public static $policy = Basic::class;

    public static string $nonce = '';

    public static function generateNonce()
    {
        static::$nonce = \bin2hex(\random_bytes(32));
    }
}