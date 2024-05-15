<?php

namespace Cels\Utilities\Tests\Directives;

use Cels\Utilities\CSP\CSP;
use Cels\Utilities\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Mockery as m;

class FontAwesomeTest extends TestCase
{
    /**
     * @var Filesystem|m\MockInterface
     */
    private $filesystem;

    /**
     * @var BladeCompiler
     */
    protected $compiler;

    private const VALID_ELEMENT_REGEX = '/^<(\w+)\s([\w\-]+(=".*?")?\s?)+?\/?>(<\/\1>)?$/';
    private const CAPTURE_ATTRIBUTES_REGEX = '/^<(\w+)\s(.+?)\/?>(<\/\1>)?$/';
    private const CAPTURE_ATTRIBUTE_REGEX = '/[\w\-]+(=".*?")?/';

    public function test_that_directive_can_be_rendered(): void
    {
        $result = $this->compiler->render('@fontawesome');

        $matches = [];
        $this->assertGreaterThan(0, \preg_match(
            self::VALID_ELEMENT_REGEX,
            $result,
            $matches,
        ));
        // should contain at least: [$result, 'script', 'src', '="url"', '</script>']
        $this->assertCount(5, $matches);
        
        $attributes = $this->attributes($result);
        $this->assertArrayHasKey('src', $attributes);
        $this->assertSame($attributes['src'], \filter_var($attributes['src'], FILTER_VALIDATE_URL));
    }

    public function test_that_directive_uses_correct_nonce(): void
    {
        CSP::$enabled = true;
        $nonce = CSP::nonce();

        $result = $this->compiler->render('@fontawesome');

        $attributes = $this->attributes($result);
        $this->assertArrayHasKey('nonce', $attributes);
        $this->assertSame($nonce, $attributes['nonce']);

        CSP::$enabled = false;
    }

    public function test_that_directive_renders_correct_subset(): void
    {
        $result = $this->compiler->render('@fontawesome(["brands", "solid", "regular"])');
        $fileSrcs = \array_map(
            fn ($_) => \array_pop($_),
            \array_map(
                fn ($_) => \explode('/', $this->attributes("{$_}>")['src']),
                \array_filter(\preg_split('/>(?!<\/)/', $result))
            ),
        );
        $this->assertCount(0, \array_filter(\array_map(
            fn ($_) => \in_array($_, $fileSrcs),
            ['brands.min.js', 'solid.min.js', 'regular.min.js', 'fontawesome.min.js'],
        ), fn ($_) => ! $_));
    }

    public function test_that_directive_renders_correct_subset_with_csp(): void
    {
        CSP::$enabled = true;
        $nonce = CSP::nonce();

        $result = $this->compiler->render('@fontawesome(["brands", "solid", "regular"])');
        $this->assertCount(0, \array_filter(
            \array_map(
                fn ($_) => $this->attributes("{$_}>")['nonce'],
                \array_filter(\preg_split('/>(?!<\/)/', $result))
            ),
            fn ($_) => $_ !== $nonce,
        ));

        CSP::$enabled = false;
    }

    // @todo: Check that custom kits & hosts config work

    protected function attributes(string $element): array
    {
        $matches = [];
        \preg_match(self::CAPTURE_ATTRIBUTES_REGEX, $element, $matches);
        $attributes = [];
        $r = $matches[2];
        do {
            $matches = [];
            if (\preg_match(self::CAPTURE_ATTRIBUTE_REGEX, \trim($r), $matches) > 0) {
                $r = \mb_substr(\trim($r), \mb_strlen($matches[0]));
                $a = \explode('=', $matches[0]);
                $attributes[\mb_strtolower(\array_shift($a))] = \mb_substr(\implode('=', $a), 1, -1);
            }
        } while ($r);

        return $attributes;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->filesystem = m::mock(Filesystem::class);
        $this->compiler = new BladeCompiler($this->filesystem, __DIR__);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}