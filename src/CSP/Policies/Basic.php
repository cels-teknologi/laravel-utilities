<?php

namespace Cels\Utilities\CSP\Policies;

use Cels\Utilities\CSP\Constants\Directive;
use Cels\Utilities\CSP\Constants\Value;
use Cels\Utilities\CSP\CSP;

class Basic extends ContentSecurityPolicy
{
    public function build()
    {
        if (! CSP::$nonce) {
            return;
        }
        $nonce = 'nonce-' . CSP::$nonce;
        return $this
            ->useDirective(Directive::BaseUri, Value::None)
            ->useDirective(Directive::ObjectSrc, Value::None)
            ->useDirective(Directive::ScriptSrc, [Value::StrictDynamic, $nonce])
            ->useDirective(Directive::StyleSrc, [Value::StrictDynamic, $nonce]);
    }
}