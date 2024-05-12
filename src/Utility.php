<?php

namespace Cels\Utilities;

use Cels\Utilities\CSP\CSP;
use Cels\Utilities\CSP\Policies\Basic;

class Utility
{
    public static function enableCSP()
    {
        CSP::$enabled = true;
    }

    public static function withCSPPolicy($cspPolicy)
    {
        CSP::$policy = $cspPolicy;
    }
}