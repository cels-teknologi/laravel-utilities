<?php

namespace Cels\Utilities\Tests;

use Cels\Utilities\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}