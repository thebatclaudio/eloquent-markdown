<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use TheBatClaudio\EloquentMarkdown\Support\MarkdownCollection;

/**
 * The service provider.
 */
final class EloquentMarkdownServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/markdown-model.php' => config_path('markdown-model.php'),
        ], 'config');

        MarkdownCollection::macro('paginate', function (int $perPage = 10) {
            /** @var MarkdownCollection $this */
            $page = LengthAwarePaginator::resolveCurrentPage();

            return new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => Request::query(),
            ]);
        });
    }
}
