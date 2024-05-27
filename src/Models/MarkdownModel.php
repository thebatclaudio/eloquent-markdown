<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Models;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use TheBatClaudio\EloquentMarkdown\Support\MarkdownCollection;

/**
 * Abstract class MarkdownModel.
 *
 * @property string $id
 * @property string $content
 * @property string $file_name
 * @property string $file_path
 */
abstract class MarkdownModel extends Model
{
    public $incrementing = false;

    public $keyType = 'string';

    public const FILE_EXTENSION = '.md';

    public const DOTS_SEPARATOR = '...';

    public const DASHES_SEPARATOR = '---';

    public const DIR_SEPARATOR = '/';

    protected static Filesystem $filesystem;

    /**
     * A collection that contains all files retrieved in the default content path.
     */
    protected static ?MarkdownCollection $allMarkdownFiles = null;

    protected $guarded = [];

    public function __construct(?string $filePath = null)
    {
        try {
            parent::__construct($filePath ? static::extractAttributes($filePath) : []);

            $this->exists = true;
        } catch (FileNotFoundException $e) {
            parent::__construct([
                'id' => static::extractFileId($filePath),
            ]);
        }
    }

    /**
     * Set filesystem.
     */
    public static function setFilesystem(Filesystem $filesystem): void
    {
        static::$filesystem = $filesystem;
    }

    /**
     * Get filesystem.
     */
    public static function getFilesystem(): Filesystem
    {
        return static::$filesystem;
    }

    /**
     * Get content path (edit this method if you need different content path for different models).
     */
    protected static function getContentPath(): string
    {
        return '';
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
    protected static function extractFileId(string $filePath): string
    {
        return static::removeFileExtension(
            Str::replace(static::getContentPath().static::DIR_SEPARATOR, '', $filePath)
        );
    }

    /**
     * Extract attributes from the YAML section of the markdown file and merge them with the real file content.
     */
    protected static function extractAttributes(string $filePath): array
    {
        $fileName = File::basename($filePath);

        $metadata = YamlFrontMatter::parse(
            static::getFilesystem()->get($filePath)
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

        $allFiles = static::getFilesystem()->allFiles($contentPath);

        // Check if we already retrieved all files
        if (! static::$allMarkdownFiles || count($allFiles) !== count(static::$allMarkdownFiles)) {
            static::$allMarkdownFiles = (new MarkdownCollection($allFiles))
                ->mapWithKeys(function ($file) {
                    return [
                        static::extractFileId($file) => new static($file),
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

        $filePath = $contentPath.static::DIR_SEPARATOR.$id.static::FILE_EXTENSION;

        static::$allMarkdownFiles[static::removeFileExtension($id)] = (static::getFilesystem()->exists($filePath)) ? new static($filePath) : null;
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
     * Get a markdown file by its id.
     */
    public static function find(string $id): ?static
    {
        static::retrieveMarkdownFile($id);

        return static::$allMarkdownFiles->get($id);
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

        return static::getContentPath().static::DIR_SEPARATOR.$this->id.static::FILE_EXTENSION;
    }

    /**
     * Save model on file.
     */
    public function save(array $options = []): bool
    {
        static::getFilesystem()->put($this->getFilePath(), $this->getFileContent());

        $this->exists = true;

        $this->finishSave($options);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function performDeleteOnModel(): void
    {
        static::getFilesystem()->delete($this->getFilePath());
    }

    /**
     * Render markdown content as HTML.
     */
    public function toHtml(): string
    {
        return app(MarkdownRenderer::class)->toHtml($this->content);
    }
}
