<?php

namespace Cels\Utilities\Services;

use Cels\Utilities\CSP\CSP;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

final class FontAwesome
{
    protected final const CACHE_KEY = 'cels-utilities___libraries__fontawesome_version';

    public function __invoke(array $args = [])
    {
        $host = '';
        $kit = Config::get('cels-utilities.libraries.endpoints.fontawesome_kit');

        if (\count($args) > 0) {
            $host = Config::get('cels-utilities.libraries.endpoints.fontawesome_host_endpoint');
        }

        if (! $kit) {
            $ver = Cache::get(self::CACHE_KEY);

            if (! $ver) {
                $ver = $this->fetchAndCache('https://api.cdnjs.com/libraries/font-awesome?fields=version');
            }

            if ($ver && !$host) {
                $host = $this->toHost($ver);
            }
        }
        
        if (!$kit && !$host) {
            return '';
        }

        if (\count($args) > 0) {
            return \implode('', \array_map(
                fn ($_) => $this->buildElement([
                    'src' => "{$host}/js/{$_}.min.js",
                    ...(CSP::$enabled ? ['data-auto-add-css' => 'false'] : []),
                ]),
                $args,
            )) . (CSP::$enabled ? $this->buildElement([
                'rel' => 'stylesheet',
                'href' => "{$host}/css/svg-with-js.min.css"
            ], 'link') : $this->buildElement(['src' => "{$host}/js/fontawesome.min.js"]));
        }
        return $this->buildElement(['src' => $kit ?: "{$host}/js/all.min.js"]);
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
            $el,
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

        $ver = \json_decode($response->getBody()->getContents())->version;
        Cache::set(self::CACHE_KEY, $ver);

        return $ver;
    }

    protected function toHost(string $ver): string
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{$ver}";
    }
}