<?php

declare(strict_types=1);

namespace TheBatClaudio\EloquentMarkdown\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class MarkdownCollection (used to add paginate method to a simple collection).
 *
 * @method LengthAwarePaginator paginate(int $perPage = 10)
 */
class MarkdownCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
