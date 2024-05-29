<?php

declare(strict_types=1);

use Carbon\Carbon;
use TheBatClaudio\EloquentMarkdown\Tests\Support\Fixture;
use TheBatClaudio\EloquentMarkdown\Tests\Support\Models\TestWithDateModel;

beforeEach(function () {
    TestWithDateModel::setFilesystem(
        Fixture::getFilesystem()
    );
});

it('returns all files with dates calling all method', function () {
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
        ->and($markdown?->toArray())
        ->toHaveKey('date')
        ->and($markdown?->date)
        ->toBeInstanceOf(Carbon::class)
        ->and(
            (new Carbon('2024-05-26'))
                ->equalTo($markdown?->date)
        )
        ->toBeTrue();
});

it('should have slug attribute', function () {
    $markdown = TestWithDateModel::find('2024-05-26-yet-another-test');

    expect($markdown)
        ->not->toBeNull()
        ->and($markdown?->toArray())
        ->toHaveKey('slug')
        ->and($markdown?->slug)
        ->toBe('yet-another-test');
});
