# Convert Markdown files to Eloquent models

[![GitHub License](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/thebatclaudio/eloquent-markdown/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/thebatclaudio/eloquent-markdown.svg)](https://packagist.org/packages/thebatclaudio/eloquent-markdown)
[![Total Downloads](https://img.shields.io/packagist/dt/thebatclaudio/eloquent-markdown)](https://packagist.org/packages/thebatclaudio/eloquent-markdown)
[![StyleCI Status](https://styleci.io/repos/792520425/shield)](https://styleci.io/repos/792520425)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/thebatclaudio/laravel-eloquent-markdown/tests.yml?branch=main&label=tests&style=flat-square)](https://img.shields.io/github/actions/workflow/status/thebatclaudio/laravel-eloquent-markdown/tests.yml?branch=main&label=tests&style=flat-square)

Easily manage Markdown files with a YAML Front Matter section using Eloquent models in your Laravel application.

## Installation

1. **Install the package via Composer**:

    ```bash
    composer require thebatclaudio/eloquent-markdown
    ```

2. **Configure the filesystem disk**

   By default, markdown files will be retrieved from `markdown` storage disk, so you need to define it in your `config/filesystems.php` file:

    ```php
    <?php
    
    return [
    
        // ...
    
        'disks' => [
            // Existing disks...
    
            'markdown' => [
                'driver' => 'local',
                'root' => storage_path('markdown'),
            ],
        ],
       
    ];
    ```

    or, if you want to use S3:

    ```php
    <?php
    
    return [
    
        // ...
    
        'disks' => [
            // Existing disks...
    
            'markdown' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
                'endpoint' => env('AWS_ENDPOINT'),
            ],
        ],
       
    ];
    ```   

4. **Publish the configuration file (optional)**

   Optionally you can publish the config file (`config/markdown.php`) to edit the default markdowns' disk with:

    ```bash
    php artisan vendor:publish --provider="TheBatClaudio\EloquentMarkdown\Providers\EloquentMarkdownServiceProvider" --tag="config"
    ```

## Usage

Create a new model that extends `TheBatClaudio\EloquentMarkdown\Models\MarkdownModel`:

```php
use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;

class Page extends MarkdownModel
{
}
```

### Get all markdown files

```php
$pages = Page::all();
```

### Get a markdown file by its id

Imagine to have a markdown file named `homepage.md` with the following content:

```yaml
---
first_attribute: First attribute
second_attribute: Second attribute
third_attribute: Third attribute
---
The time has come. You know it. In your mind. In your heart. In your soul. You tried to hold me back. But you can't,
Bruce. You're weak. You're a shell. A rusty trap that cannot hold me. Let it go. Let me OUT.
```

You will get the file as Eloquent model using the `find` method:
```php
$homepage = Page::find('homepage');
```

And you will find the following attributes:

```php
echo $homepage->id; // homepage
echo $homepage->file_name; // homepage.md
echo $homepage->file_path; // { YOUR STORAGE PATH }/homepage.md
echo $homepage->first_attribute; // First attribute
echo $homepage->second_attribute; // Second attribute
echo $homepage->third_attribute; // Third attribute
echo $homepage->content; // The time has come. You know it [...]
```

### Update a markdown file

```php
$homepage = Page::find('homepage');

$homepage->update([
    'attribute' => 'new value'
])

// or
$homepage->attribute = 'new value';
$homepage->save();
```

### Delete a markdown file
```php
$homepage = Page::find('homepage');
$homepage->delete();
```

### Create a markdown file

```php
$newHomepage = new Page();
$newHomepage->id = 'homepage';
$newHomepage->title = 'Homepage';
$newHomepage->content = 'Content';
$newHomepage->save();
```

### Using Markdown with dates (e.g. `YYYY-MM-DD-your-markdown.md`)

Create a new model that extends `TheBatClaudio\EloquentMarkdown\Models\MarkdownModel` and uses `TheBatClaudio\EloquentMarkdown\Models\Traits\WithDate` trait:

```php
use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;
use TheBatClaudio\EloquentMarkdown\Models\Traits\WithDate;

class Article extends MarkdownModel
{
    use WithDate;
}
```

You will find two new attributes inside your model:
- `date`: a Carbon instance with the date defined on your markdown file name (e.g. `2024-05-15` for `2024-05-15-your-markdown.md`)
- `slug`: the slug of your markdown (e.g. `your-markdown` for `2024-05-15-your-markdown.md`)

### Different path for different models

You can extend `getContentPath` inside your model to use different paths for different models:

```php
use TheBatClaudio\EloquentMarkdown\Models\MarkdownModel;
use TheBatClaudio\EloquentMarkdown\Models\Traits\WithDate;

class Article extends MarkdownModel
{
    use WithDate;

    protected static function getContentPath(): string
    {
        return 'articles';
    }
}

class Page extends MarkdownModel
{
    protected static function getContentPath(): string
    {
        return 'pages';
    }
}
```

## Credits

- [Claudio La Barbera](https://github.com/thebatclaudio)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
