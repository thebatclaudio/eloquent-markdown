# Convert Markdown files to Eloquent models

<p>
  <a href="https://raw.githubusercontent.com/thebatclaudio/eloquent-markdown/LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="GitHub License" /></a>
  <a href="https://packagist.org/packages/thebatclaudio/eloquent-markdown"><img src="https://img.shields.io/packagist/v/thebatclaudio/eloquent-markdown.svg" alt="Latest stable version" /></a>
  <a href="https://packagist.org/packages/thebatclaudio/eloquent-markdown"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/thebatclaudio/eloquent-markdown"></a>
  <a href="https://styleci.io/repos/792520425"><img src="https://styleci.io/repos/792520425/shield" alt="StyleCI Status" /></a>
  <a href="https://img.shields.io/github/actions/workflow/status/thebatclaudio/laravel-eloquent-markdown/tests.yml?branch=main&label=tests&style=flat-square"><img src="https://img.shields.io/github/actions/workflow/status/thebatclaudio/laravel-eloquent-markdown/tests.yml?branch=main&label=tests&style=flat-square" alt="GitHub Workflow Status" /></a>
</p>

Easily manage Markdown files with YAML Front Matter section using Eloquent Models.

## Installation

You can install the package via composer:

```bash
composer require thebatclaudio/eloquent-markdown
```

By default, markdown files will be retrieved from `resources/markdown`. Optionally you can publish the config file (`config/markdown-model.php`) and
edit the default markdown's location with:

```bash
php artisan vendor:publish --provider="TheBatClaudio\EloquentMarkdown\Providers\EloquentMarkdownServiceProvider" --tag="config"
```

## Usage

Create a new model that extends `TheBatClaudio\EloquentMarkdown\Models\MarkdownModel`:

```php
class Page extends TheBatClaudio\EloquentMarkdown\Models\MarkdownModel
{
}
```

```php
// Get all markdown files
Page::all();

// Get a markdown file by its slug (example: homepage.md)
$homepage = Page::find('homepage');

// Get Yaml Front Matter metadata
echo $homepage->title;
echo $homepage->description;
echo $homepage->attribute;

// Get file content
echo $homepage->content;

// Update an attribute
$homepage->update([
    'attribute' => 'new value'
])

// or
$homepage->attribute = 'new value';
$homepage->save();

// Delete file
$homepage->delete();

// Create a new file
$newHomepage = new Page();
$newHomepage->id = 'homepage';
$newHomepage->title = 'Homepage';
$newHomepage->content = 'Content';
$newHomepage->save();
```

### Markdown with dates (e.g. YYYY-MM-DD-your-markdown)

Create a new model that extends `TheBatClaudio\EloquentMarkdown\Models\MarkdownWithDateModel`:

```php
class Article extends TheBatClaudio\EloquentMarkdown\Models\MarkdownWithDateModel
{
}
```

You will find two new attributes inside your model:
- `date`: a Carbon instance with the date defined on your markdown file name (e.g. `2024-05-15` for `2024-05-15-your-markdown.md`)
- `slug`: the slug of your markdown (e.g. `your-markdown` for `2024-05-15-your-markdown.md`)

### Different path for different models

You can extend `getContentPath` inside your model to use a different path instead of the one defined on `config/markdown-model.php`

```php
class Article extends TheBatClaudio\EloquentMarkdown\Models\MarkdownWithDateModel
{
    protected static function getContentPath(): string
    {
        return resource_path('posts');
    }
}

class Page extends TheBatClaudio\EloquentMarkdown\Models\MarkdownModel
{
    protected static function getContentPath(): string
    {
        return resource_path('pages');
    }
}
```

## Credits

- [Claudio La Barbera](https://github.com/thebatclaudio)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
