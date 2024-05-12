<?php

namespace Cels\Utilities\CSP\Policies;

use Cels\Utilities\CSP\Constants\Directive;
use Cels\Utilities\CSP\Constants\Value;
use Cels\Utilities\CSP\CSP;

class Basic extends ContentSecurityPolicy
{
    public function build(string $nonce)
    {
        $nonce = 'nonce-' . $nonce;
        return $this
            ->useDirective(Directive::BaseUri, Value::None)
            ->useDirective(Directive::ObjectSrc, Value::None)
            ->useDirective(Directive::ScriptSrc, [Value::StrictDynamic, $nonce, 'https:'])
            ->useDirective(Directive::StyleSrc, [Value::StrictDynamic, $nonce, 'https:']);
    }
}