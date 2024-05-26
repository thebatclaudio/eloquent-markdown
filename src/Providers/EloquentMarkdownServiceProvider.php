<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;

/**
 * The service provider.
 */
final class EloquentMarkdownServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/markdown.php' => config_path('markdown.php'),
        ], 'config');
    }

    /**
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        parent::register();

        $this->configureFilesystem();
    }

    /**
     * @throws BindingResolutionException
     */
    private function configureFilesystem(): void
    {
        MarkdownModel::setFilesystem(
            $this->app->make('filesystem')->disk(config('markdown.disk'))
        );
    }
}
