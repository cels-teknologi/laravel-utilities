<?php

namespace Cels\Utilities;

use Cels\Utilities\CSP\CSP;
use Cels\Utilities\CSP\Policies\Basic;

class Utility
{
    public static function enableCSP()
    {
        CSP::enable();
    }

    public static function withCSPPolicy($cspPolicy)
    {
        CSP::$policy = $cspPolicy;
    }
}