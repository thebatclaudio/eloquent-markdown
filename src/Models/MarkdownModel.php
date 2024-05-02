<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use TheBatClaudio\EloquentMarkdown\Support\MarkdownCollection;

/**
 * Class MarkdownModel.
 */
abstract class MarkdownModel extends Model
{
    public $incrementing = false;

    public $keyType = 'string';

    public const FILE_EXTENSION = '.md';

    public const DOTS_SEPARATOR = '...';

    public const DASHES_SEPARATOR = '---';

    /**
     * A collection that contains all files retrieved in the default content path.
     */
    protected static ?MarkdownCollection $allMarkdownFiles = null;

    protected $guarded = [];

    public function __construct(?string $filename = null)
    {
        parent::__construct($filename ? static::extractAttributes($filename) : []);
    }

    /**
     * Get content path (edit this method if you need different content path for different models).
     */
    private static function getContentPath(): string
    {
        return config('markdown-model.path');
    }

    /**
     * Remove markdown file extension (.md).
     */
    private static function removeFileExtension(string $filename): string
    {
        return Str::replace(static::FILE_EXTENSION, '', $filename);
    }

    /**
     * Extract file ID from file's path (e.g 'category/page' is the ID of './category/page.md').
     */
    private static function extractFileId(string $filePath): string
    {
        return static::removeFileExtension(
            Str::replace(static::getContentPath().'/', '', $filePath)
        );
    }

    /**
     * Extract attributes from the YAML section of the markdown file and merge them with the real file content.
     */
    protected static function extractAttributes(string $filePath): array
    {
        $fileName = File::basename($filePath);

        $metadata = YamlFrontMatter::parse(
            File::get($filePath)
        );

        return array_merge(
            $metadata->matter(),
            [
                'content' => $metadata->body(),
                'file_path' => $filePath,
                'file_name' => $fileName,
                'id' => static::extractFileId($filePath),
            ]
        );
    }

    /**
     * Retrieve markdown files and set $allMarkdownFiles static variable.
     */
    private static function retrieveMarkdownFiles(): void
    {
        $contentPath = static::getContentPath();

        $allFiles = File::allFiles($contentPath);

        // Check if we already retrieved all files
        if (! static::$allMarkdownFiles || count($allFiles) !== count(static::$allMarkdownFiles)) {
            static::$allMarkdownFiles = (new MarkdownCollection($allFiles))
                ->filter(function ($file) {
                    return $file->isFile();
                })
                ->sortByDesc(function ($file) {
                    return $file->getBaseName();
                })
                ->mapWithKeys(function ($file) use ($contentPath) {
                    return [
                        static::extractFileId($file->getPathName()) => new static($contentPath.'/'.$file->getBaseName()),
                    ];
                });
        }
    }

    /**
     * Retrieve a single markdown file by ID.
     */
    private static function retrieveMarkdownFile(string $id): void
    {
        $contentPath = static::getContentPath();

        if (! static::$allMarkdownFiles) {
            static::$allMarkdownFiles = new MarkdownCollection();
        }

        static::$allMarkdownFiles[static::removeFileExtension($id)] = new static($contentPath.'/'.$id.static::FILE_EXTENSION);
    }

    /**
     * Get all markdown files.
     *
     * @param  array  $columns
     */
    public static function all($columns = ['*']): MarkdownCollection
    {
        static::retrieveMarkdownFiles();

        return static::$allMarkdownFiles;
    }

    /**
     * Get a markdown file by its slug.
     */
    public static function find(string $slug): ?static
    {
        if (! static::$allMarkdownFiles) {
            static::retrieveMarkdownFile($slug);
        }

        return static::$allMarkdownFiles->get($slug);
    }

    /**
     * Get file content mixing YAML Front Matter attributes and markdown content.
     */
    private function getFileContent(): string
    {
        $content = Str::replace(
            static::DOTS_SEPARATOR,
            static::DASHES_SEPARATOR,
            yaml_emit(
                Arr::except(
                    $this->getAttributes(),
                    [
                        'id',
                        'content',
                    ]
                )
            )."\n"
        );
        $content .= $this->content;

        return $content;
    }

    /**
     * Get file path.
     */
    private function getFilePath(): string
    {
        if ($this->file_path) {
            return $this->file_path;
        }

        return static::getContentPath().'/'.$this->id.static::FILE_EXTENSION;
    }

    /**
     * Save model on file.
     */
    public function save(array $options = []): bool
    {
        $saved = File::put($this->getFilePath(), $this->getFileContent());

        if ($saved) {
            $this->exists = true;

            $this->finishSave($options);
        }

        return (bool) $saved;
    }

    /**
     * {@inheritdoc}
     */
    protected function performDeleteOnModel(): void
    {
        unlink($this->getFilePath());
    }
}
