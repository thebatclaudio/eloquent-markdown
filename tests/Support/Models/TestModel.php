<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Tests\Support\Models;

use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;

class TestModel extends MarkdownModel
{
    protected static function getContentPath(): string
    {
        return 'pages';
    }
}
