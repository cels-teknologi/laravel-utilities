<?php

namespace Cels\Utilities\CSP\Views\Components;

use Cels\Utilities\CSP\CSP;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Style extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return sprintf('<style {{ $attributes }} nonce="%s">{{ $slot }}</style>', CSP::getSharedNonce());
    }
}
