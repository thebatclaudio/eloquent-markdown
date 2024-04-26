<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Tests;

use Orchestra\Testbench\TestCase;
use TheBatClaudio\EloquentMarkdown\Providers\EloquentMarkdownServiceProvider;

class EloquentMarkdownTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EloquentMarkdownServiceProvider::class,
        ];
    }
}
