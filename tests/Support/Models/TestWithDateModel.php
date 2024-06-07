<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Tests\Support\Models;

use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;
use TheBatClaudio\EloquentMarkdown\Models\Traits\WithDate;

class TestWithDateModel extends MarkdownModel
{
    use WithDate;

    protected static function getContentPath(): string
    {
        return 'blog';
    }
}
