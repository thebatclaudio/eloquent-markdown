<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

/**
 * Class MarkdownCollection (used to add paginate method to a simple collection).
 */
class MarkdownCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        /** @var MarkdownCollection $this */
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => Request::query(),
        ]);
    }
}
