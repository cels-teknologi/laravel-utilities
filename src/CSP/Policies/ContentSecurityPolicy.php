<?php

namespace Cels\Utilities\CSP\Policies;

use Cels\Utilities\CSP\Constants\Directive;
use Cels\Utilities\CSP\Constants\Value;
use Illuminate\Support\Arr;

abstract class ContentSecurityPolicy implements \Stringable
{
    protected array $directives = [];

    abstract public function build(string $nonce);

    protected function useDirective(
        Directive $directive,
        Value | string | array $values,
    ): self {
        $v = \is_array($values) ? $values : [$values];
        $v = Arr::flatten(\array_map(fn ($_) => (
            \is_string($_)
                ? \explode(' ', $_)
                : $_
        ), $v));
        $this->validateValues($v);

        $this->directives[] = [$directive, $v];

        return $this;
    }

    protected function validateValues(array $values): void
    {
        $v = \array_map(fn ($_) => (
            $_ instanceof Value
                ? $_->value
                : $_
        ), $values);
        if (\in_array((Value::None)->value, $v, true) && \count($v) > 1) {
            throw new \InvalidArgumentException('If exists, \'none\' must be the only value in a directive.');
        }
    }

    public function quote(string $str)
    {
        return \mb_ereg('^[^:]+:$', $str) || \filter_var($str, FILTER_VALIDATE_URL)
            ? $str
            : "'{$str}'";
    }

    public function __toString(): string
    {
        return \implode('; ', \array_map(
            fn ($v) => $v[0]->value . (
                \count($v[1]) <= 0
                    ? ''
                    : ' ' . \implode(' ', \array_map(
                        fn ($_) => $_ instanceof Value ? "'{$_->value}'" : $this->quote($_),
                        $v[1],
                    ))
            ),
            $this->directives,
        ));
    }
}