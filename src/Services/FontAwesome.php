<?php

namespace Cels\Utilities\Services;

use Cels\Utilities\CSP\CSP;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

final class FontAwesome implements Htmlable, \Stringable
{
    protected final const CACHE_KEY = 'cels-utilities___libraries__endpoints_fontawesome';

    public function __invoke(array $args = [])
    {
        if (\count($args) > 0) {
            $host = Config::get('cels-utilities.libraries.endpoints.fontawesome_host_endpoint');
            return \implode('', \array_map(
                fn ($_) => $this->buildElement([
                    'src' => "{$host}/js/{$_}.min.js",
                    'data-auto-add-css' => 'false',
                ]),
                $args,
            )) . $this->buildElement([
                'rel' => 'stylesheet',
                'href' => "{$host}/css/svg-with-js.min.css"
            ], 'link');
        }

        $src = Config::get('cels-utilities.libraries.endpoints.fontawesome_kit');
        if (! $src) {
            $src = Cache::get(self::CACHE_KEY);

            if (! $src) {
                $src = $this->fetchAndCache('https://api.cdnjs.com/libraries/font-awesome?fields=version');
            }
        }
        
        if (! $src) {
            return '';
        }

        return $this->buildElement(['src' => $src]);
    }

    /**
     * Get the Fontawesome tag content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->__invoke();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    protected function buildElement(array $attrs = [], string $el = 'script'): string
    {
        $a = [
            ...$attrs,
            'crossorigin' => 'anonymous',
            'referrerpolicy' => 'no-referrer',
            ...(CSP::$enabled ? ['nonce' => CSP::getSharedNonce()] : []),
        ];
        return sprintf(
            '<%s %s>',
            \implode(' ', \array_map(
                fn ($_) => (\is_int($_)
                    ? $a[$_]
                    : sprintf('%s="%s"', $_, $a[$_])
                ),
                \array_keys($a),
            )) . match ($el) {
                // For scripts, we're making it <...' crossorigin async defer></script'>
                'script' => ' async defer></script',
                // For links, we're making it <...' /'>
                'link' => ' /',
            },
        );
    }

    protected function fetchAndCache(string $url): ?string
    {
        $response = (new Client([
            'timeout' => Config::get('cels-utilities.libraries.timeout', 10),
        ]))->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $src = $this->parseResponse(
            \json_decode($response->getBody()->getContents())->version,
        );
        Cache::set(self::CACHE_KEY, $src);

        return $src;
    }

    protected function parseResponse(string $ver)
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{$ver}/js/all.min.js";
    }
}