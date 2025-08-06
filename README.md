# Filament Docs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eightynine/filament-docs.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-docs)
[![Total Downloads](https://img.shields.io/packagist/dt/eightynine/filament-docs.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-docs)

A Filament plugin for creating elegant documentation pages within your admin panel.

## Features

- **Markdown Support**: Full CommonMark compatibility with syntax highlighting
- **Real-time Search**: Instant search with context-aware results
- **Responsive Design**: Mobile-first with dark mode support
- **Multi-language**: Built-in internationalization support
- **Theme Integration**: Seamlessly integrates with Filament themes
- **Artisan Commands**: CLI tools for generating pages and content

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+

## Installation

```bash
composer require eightynine/filament-docs
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag="filament-docs-config"
```

Create documentation directory:

```bash
mkdir -p resources/docs
```

## Quick Start

1. Create a documentation page:

```bash
php artisan make:filament-docs-page UserManual \
    --navigation-group="Documentation" \
    --navigation-icon="heroicon-o-book-open"
```

2. Add markdown files to `resources/docs/`:

```markdown
# Getting Started

Welcome to the documentation!
```

3. Access your documentation in the Filament admin panel.

## Configuration

The configuration file `config/filament-docs.php` allows customization:

```php
return [
    'default_docs_path' => resource_path('docs'),
    'search' => [
        'debounce_ms' => 300,
    ],
    'ui' => [
        'sidebar_width' => 'lg:w-80',
        'default_navigation_group' => 'Documentation',
    ],
];
```

## Advanced Usage

### Custom Documentation Page

```php
<?php

namespace App\Filament\Pages;

use EightyNine\FilamentDocs\Pages\DocsPage;

class UserManual extends DocsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Documentation';
    protected static ?string $title = 'User Manual';

    protected function getDocsPath(): string
    {
        return resource_path('user-manual');
    }

    protected function getSectionOrder(string $filename): int
    {
        return match($filename) {
            'introduction' => 1,
            'installation' => 2,
            'usage' => 3,
            default => 99,
        };
    }
}
```

### Multi-language Support

```php
// config/filament-docs.php
return [
    'localization' => [
        'supported_locales' => ['en', 'es', 'fr'],
        'locale_paths' => [
            'en' => 'docs/en',
            'es' => 'docs/es',
            'fr' => 'docs/fr',
        ],
    ],
];
```

Directory structure:
```
resources/docs/
├── en/
│   ├── getting-started.md
│   └── user-guide.md
├── es/
│   ├── getting-started.md
│   └── user-guide.md
└── fr/
    ├── getting-started.md
    └── user-guide.md
```

## Commands

### Create Documentation Page

```bash
# Basic page
php artisan make:filament-docs-page MyDocs

# With options
php artisan make:filament-docs-page ApiDocs \
    --navigation-group="Developer" \
    --navigation-icon="heroicon-o-code-bracket" \
    --title="API Documentation" \
    --slug="api-docs"
```

### Create Markdown Content

```bash
# Basic markdown file
php artisan make:filament-docs-markdown "Getting Started"

# With template
php artisan make:filament-docs-markdown "API Guide" --template=api
```

Available templates: `basic`, `guide`, `api`, `troubleshooting`, `feature`

## Customization

### Custom Views

Publish views for customization:

```bash
php artisan vendor:publish --tag="filament-docs-views"
```

### Custom Styling

Publish assets:

```bash
php artisan vendor:publish --tag="filament-docs-assets"
```

Add custom CSS:

```css
.docs-container {
    @apply max-w-7xl mx-auto;
}

.docs-sidebar {
    @apply w-64 bg-white dark:bg-gray-900;
}
```

### Performance Optimization

Enable caching for large documentation:

```php
class CachedDocsPage extends DocsPage
{
    protected function getCachedContent(string $filename): string
    {
        return Cache::remember(
            "docs.content.{$filename}",
            3600,
            fn() => $this->loadAndProcessMarkdown($filename)
        );
    }
}
```

## License

MIT License. See [LICENSE.md](LICENSE.md) for details.
