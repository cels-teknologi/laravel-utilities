<?php

namespace Cels\Utilities\CSP\Views\Components;

use Cels\Utilities\CSP\CSP;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Script extends Component
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
        return sprintf('<script {{ $attributes }} nonce="%s">{{ $slot }}</script>', CSP::getSharedNonce());
    }
}
