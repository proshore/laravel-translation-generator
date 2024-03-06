<?php

namespace Proshore\Translator\Tests;

use Proshore\Translator\TranslationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [TranslationServiceProvider::class];
    }
}
