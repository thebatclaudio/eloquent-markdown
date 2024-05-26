<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use TheBatClaudio\EloquentMarkdown\Models\MarkdownWithDateModel;

class TestWithDateModel extends MarkdownWithDateModel
{
}

beforeEach(function () {
    Config::set('markdown-model.path', __DIR__.'/../content/blog');
});

it('returns all files calling all method', function () {
    $markdowns = TestWithDateModel::all()->toArray();

    expect($markdowns)
        ->not->toBeEmpty()
        ->toHaveCount(3)
        ->and(array_keys($markdowns))
        ->toContain(
            '2024-05-26-yet-another-test',
            '2024-05-24-another-test',
            '2024-05-22-test',
        );
});

it('should have date attribute', function () {
    $markdown = TestWithDateModel::find('2024-05-26-yet-another-test');

    expect($markdown)
        ->not->toBeNull()
        ->and($markdown->toArray())
        ->toHaveKey('date')
        ->and($markdown->date)
        ->toBeInstanceOf(Carbon::class)
        ->and(
            (new Carbon('2024-05-26'))
                ->equalTo($markdown->date)
        )
        ->toBeTrue();
});

it('should have slug attribute', function () {
    $markdown = TestWithDateModel::find('2024-05-26-yet-another-test');

    expect($markdown)
        ->not->toBeNull()
        ->and($markdown->toArray())
        ->toHaveKey('slug')
        ->and($markdown->slug)
        ->toBe('yet-another-test');
});
