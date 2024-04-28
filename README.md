# [WIP] Convert Markdown files to Eloquent models

<p>
  <a href="https://styleci.io/repos/792520425"><img src="https://styleci.io/repos/792520425/shield" alt="StyleCI Status"></img></a>
  <a href="https://img.shields.io/github/actions/workflow/status/thebatclaudio/laravel-eloquent-markdown/tests.yml?branch=main&label=tests&style=flat-square"><img src="https://img.shields.io/github/actions/workflow/status/thebatclaudio/laravel-eloquent-markdown/tests.yml?branch=main&label=tests&style=flat-square" alt="GitHub Workflow Status"></img></a>
</p>

Easily manage Markdown files using Eloquent Models.

```php
// Get all markdown files
MarkdownModel::all();

// Get a markdown file by its slug (example: homepage.md)
$homepage = MarkdownModel::find('homepage');

// Get yaml metadata
echo $homepage->title;
echo $homepage->description;
echo $homepage->attribute;

// Get file content
echo $homepage->content;
```

## Installation

You can install the package via composer:

```bash
composer require thebatclaudio/laravel-eloquent-markdown
```

By default, markdown files will be retrieved from `resources/markdown`. Optionally you can publish the config file and
edit the default markdown's location with:

```bash
php artisan vendor:publish --provider="TheBatClaudio\EloquentMarkdown\Providers\EloquentMarkdownServiceProvider" --tag="config"
```

## Credits

- [Claudio La Barbera](https://github.com/thebatclaudio)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.