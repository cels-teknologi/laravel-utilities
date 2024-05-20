<?php

namespace Cels\Utilities\CSP;

use Cels\Utilities\CSP\Middleware\AddCSPHeaders;
use Cels\Utilities\CSP\Policies\Basic;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;

class CSP
{
    public const SINGLETON_KEY = 'cels-utilities_csp-nonce';
    public const VIEW_SHARE_VARIABLE_KEY = '_cels_utilities__cspNonce';

    public static bool $share = true;

    public static bool $enabled = false;

    public static $policy = Basic::class;

    public static function enable()
    {
        self::$enabled = true;
        app('router')->prependMiddlewareToGroup('web', AddCSPHeaders::class);
    }

    public static function nonce(): string
    {
        $nonce = \bin2hex(\random_bytes(32));

        if (self::$share) {
            View::share(self::VIEW_SHARE_VARIABLE_KEY, $nonce);
            Vite::useCspNonce($nonce);
            Blade::directive('celsCspNonceAttr', fn () => sprintf('nonce="$%s"', CSP::VIEW_SHARE_VARIABLE_KEY));
        }

        return $nonce;
    }

    public static function getSharedNonce(): string
    {
        $shared = View::getShared();
        if (! \array_key_exists(self::VIEW_SHARE_VARIABLE_KEY, $shared)) {
            return '';
        }
        return $shared[self::VIEW_SHARE_VARIABLE_KEY];
    }
}