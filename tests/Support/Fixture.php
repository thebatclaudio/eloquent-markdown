<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Tests\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class Fixture
{
    protected static ?Filesystem $filesystem = null;

    public static function getFilesystem(): Filesystem
    {
        if (! static::$filesystem) {
            static::$filesystem = Storage::build([
                'driver' => 'local',
                'root' => './tests/fixtures/content/',
            ]);
        }

        return static::$filesystem;
    }
}
