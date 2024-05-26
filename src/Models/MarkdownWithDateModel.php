<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Abstract class MarkdownWithDateModel.
 *
 * @property Carbon $date
 * @property string $slug
 */
abstract class MarkdownWithDateModel extends MarkdownModel
{
    public static function extractDate(string $filePath): Carbon
    {
        preg_match("/\d{4}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])/", $filePath, $date);

        return new Carbon($date[0]);
    }

    public static function extractSlug(string $filePath): string
    {
        return Str::substr(static::extractFileId($filePath), 11);
    }

    protected static function extractAttributes(string $filePath): array
    {
        return array_merge(
            parent::extractAttributes($filePath),
            [
                'date' => static::extractDate($filePath),
                'slug' => self::extractSlug($filePath),
            ]
        );
    }
}
