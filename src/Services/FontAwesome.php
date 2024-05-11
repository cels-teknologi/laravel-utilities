<?php

namespace Cels\Utilities\Services;

use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

final class FontAwesome implements Htmlable, \Stringable
{
    protected final const CACHE_KEY = 'cels-utilities___libraries__endpoints_fontawesome';

    public function __invoke()
    {
        $src = Config::get('cels-utilities.libraries.endpoints.fontawesome');
        if (! $src) {
            $src = Cache::get(self::CACHE_KEY);

            if (! $src) {
                $src = $this->fetchAndCache('https://api.cdnjs.com/libraries/font-awesome?fields=version');
            }
        }
        
        if (! $src) {
            return '';
        }

        return sprintf(<<<HTML
<script src="%s" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
HTML, $src);
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

    protected function fetchAndCache(string $url): ?string
    {
        $response = (new Client([
            'timeout' => Config::get('cels-utilities.libraries.timeout', 10),
        ]))->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $src = $this->parseResponse(\json_decode(
            $response->getBody()->getContents(),
            associative: true,
        )['version']);
        Cache::set(self::CACHE_KEY, $src);

        return $src;
    }

    protected function parseResponse(string $ver)
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{$ver}/js/all.min.js";
    }
}