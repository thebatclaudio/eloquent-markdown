<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use TheBatClaudio\EloquentMarkdown\MarkdownModel;

beforeEach(function () {
    Config::set('markdown-model.path', __DIR__.'/../content/');
});

it('extracts right attributes', function () {
    $model = new MarkdownModel(__DIR__.'/../content/test.md');

    expect($model)
        ->toBeInstanceOf(MarkdownModel::class)
        ->and($model->toArray())
        ->toHaveAttribute('first_attribute')
        ->toHaveAttribute('second_attribute')
        ->toHaveAttribute('third_attribute')
        ->toHaveAttribute('content');
});

it('returns all files calling all methods', function () {
    $markdowns = MarkdownModel::all()->toArray();

    expect($markdowns)
        ->not->toBeEmpty()
        ->toHaveCount(3)
        ->and(array_keys($markdowns))
        ->toContain(
            'test',
            'test2',
            'test3'
        );
});
