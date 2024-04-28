<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use TheBatClaudio\EloquentMarkdown\Support\MarkdownCollection;

/**
 * Class MarkdownModel.
 */
class MarkdownModel extends Model
{
    /**
     * A collection that contains all files retrieved in the default content path.
     */
    protected static ?MarkdownCollection $allMarkdownFiles = null;

    protected $guarded = [];

    public function __construct(?string $filename = null)
    {
        parent::__construct($filename ? self::extractAttributes($filename) : []);
    }

    /**
     * Remove markdown file extension (.md).
     */
    private static function removeFileExtension(string $filename): string
    {
        return str_replace('.md', '', $filename);
    }

    /**
     * Extract attributes from the YAML section of the markdown file and merge them with the real file content.
     */
    private static function extractAttributes(string $filename): array
    {
        $metadata = YamlFrontMatter::parse(
            file_get_contents(
                $filename
            )
        );

        return array_merge(
            $metadata->matter(),
            [
                'content' => $metadata->body(),
            ]
        );
    }

    private static function retrieveMarkdownFiles(): void
    {
        $contentPath = config('markdown-model.path');

        self::$allMarkdownFiles = (new MarkdownCollection(File::allFiles($contentPath)))
            ->sortByDesc(function ($file) {
                return $file->getBaseName();
            })
            ->mapWithKeys(function ($file) use ($contentPath) {
                return [
                    self::removeFileExtension($file->getBaseName()) => new self($contentPath . '/' . $file->getBaseName()),
                ];
            });
    }

    public static function all($columns = ['*']): MarkdownCollection
    {
        if (!self::$allMarkdownFiles) {
            static::retrieveMarkdownFiles();
        }

        return self::$allMarkdownFiles;
    }

    public static function find(string $slug): ?MarkdownModel
    {
        if (!self::$allMarkdownFiles) {
            static::retrieveMarkdownFiles();
        }

        return self::$allMarkdownFiles->get($slug);
    }
}
