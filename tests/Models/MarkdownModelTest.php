<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;

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

it('returns the right file calling find method', function () {
    $markdown = MarkdownModel::find('test');

    expect($markdown)
        ->toBeInstanceOf(MarkdownModel::class)
        ->not->toBeEmpty()
        ->and($markdown?->toArray())
        ->toHaveAttribute('first_attribute')
        ->toHaveAttribute('second_attribute')
        ->toHaveAttribute('third_attribute')
        ->toHaveAttribute('content')
        ->and($markdown?->first_attribute)
        ->toBe('First attribute')
        ->and($markdown?->second_attribute)
        ->toBe('Second attribute')
        ->and($markdown?->third_attribute)
        ->toBe('Third attribute')
        ->and($markdown->content)
        ->toContain('The time has come');
});
