# Filament Docs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eightynine/filament-docs.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-docs)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eightynine/filament-docs/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eightynine/filament-docs/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eightynine/filament-docs/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eightynine/filament-docs/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eightynine/filament-docs.svg?style=flat-square)](https://packagist.org/packages/eightynine/filament-docs)

Elegant documentation system for your Filament application with search, navigation, and markdown support.

## Features

- 📚 **Markdown Support**: Write documentation in markdown format
- 🔍 **Powerful Search**: Real-time search across all documentation with highlighting
- 🧭 **Smart Navigation**: Sidebar navigation with section progress tracking
- 📱 **Responsive Design**: Works beautifully on desktop and mobile devices
- 🎨 **Customizable**: Easy to customize and extend
- 🌐 **Internationalization**: Full translation support
- ⚡ **Performance**: Optimized loading and caching
- 🔧 **Commands**: Artisan commands to generate pages and markdown files

## Installation

You can install the package via composer:

```bash
composer require eightynine/filament-docs
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-docs-config"
```

You can publish the views with:

```bash
php artisan vendor:publish --tag="filament-docs-views"
```

You can publish the translations with:

```bash
php artisan vendor:publish --tag="filament-docs-translations"
```

This is the contents of the published config file:

```php
<?php

return [
    'default_docs_path' => resource_path('docs'),
    
    'markdown' => [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 10,
    ],
    
    'search' => [
        'debounce_ms' => 300,
        'max_results_per_section' => 3,
        'highlight_class' => 'bg-yellow-200 text-yellow-800 px-1 rounded',
    ],
    
    // ... more configuration options
];
```

## Usage

### Creating a Documentation Page

Use the artisan command to create a new documentation page:

```bash
php artisan make:filament-docs-page UserManual --navigation-group="Help & Documentation" --navigation-icon="heroicon-o-book-open"
```

This will create:
- A new Filament page class
- A corresponding Blade view
- A sample markdown file
- The documentation directory structure

### Creating Markdown Files

Use the artisan command to create new markdown documentation files:

```bash
# Create a basic markdown file
php artisan make:filament-docs-markdown "Getting Started"

# Create a guide with template
php artisan make:filament-docs-markdown "Installation Guide" --template=guide

# Create API documentation
php artisan make:filament-docs-markdown "API Reference" --template=api

# Create troubleshooting documentation
php artisan make:filament-docs-markdown "Troubleshooting" --template=troubleshooting
```

Available templates:
- `basic` - Simple documentation template
- `guide` - Step-by-step guide template
- `api` - API reference template
- `troubleshooting` - Troubleshooting guide template
- `feature` - Feature documentation template

### Custom Documentation Page

Create a custom documentation page by extending the `DocsPage` class:

```php
<?php

namespace App\Filament\Pages;

use EightyNine\FilamentDocs\Pages\DocsPage;

class UserManual extends DocsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Help & Documentation';
    protected static ?string $title = 'User Manual';
    protected static ?string $slug = 'user-manual';

    public function getTitle(): string
    {
        return 'User Manual';
    }

    public function getHeading(): string
    {
        return 'User Manual & Documentation';
    }

    public function getSubheading(): ?string
    {
        return 'Complete guide to using the system';
    }

    /**
     * Get the path to markdown files
     */
    protected function getDocsPath(): string
    {
        return resource_path('user-manual');
    }
    
    /**
     * Customize section ordering
     */
    protected function getSectionOrder(string $filename): int
    {
        $orderMap = [
            'getting-started' => 1,
            'user-roles-permissions' => 2,
            'dashboard-overview' => 3,
            // ... more sections
        ];
        
        return $orderMap[$filename] ?? 99;
    }
}
```

### Markdown File Structure

Place your markdown files in the specified documentation directory (default: `resources/docs`):

```
resources/
└── docs/
    ├── getting-started.md
    ├── installation.md
    ├── configuration.md
    ├── usage.md
    ├── api-reference.md
    └── troubleshooting.md
```

### Markdown File Format

Each markdown file should start with a heading:

```markdown
# Getting Started

## Overview

Welcome to the documentation!

## Installation

Step-by-step installation instructions...

### Prerequisites

- PHP 8.2+
- Laravel 10+
- Filament 3+

## Configuration

Configuration details...
```

## Customization

### Custom Styling

You can customize the appearance by publishing the CSS file and modifying it:

```bash
php artisan vendor:publish --tag="filament-docs-assets"
```

### Custom Section Ordering

Override the `getSectionOrder()` method in your documentation page:

```php
protected function getSectionOrder(string $filename): int
{
    $orderMap = [
        'introduction' => 1,
        'getting-started' => 2,
        'advanced-topics' => 3,
        // ... your custom order
    ];
    
    return $orderMap[$filename] ?? 99;
}
```

### Custom Search Highlighting

Configure search highlighting in the config file:

```php
'search' => [
    'highlight_class' => 'bg-blue-200 text-blue-800 px-1 rounded',
],
```

### Internationalization

The package supports full internationalization. Publish the language files and translate them:

```bash
php artisan vendor:publish --tag="filament-docs-translations"
```

Supported languages:
- English (en) - Default
- Add your own language files in `resources/lang/vendor/filament-docs/`

## Advanced Features

### Search Functionality

The package includes powerful search capabilities:
- Real-time search with debouncing
- Content highlighting
- Section-specific results
- Line number references
- Smart result limiting

### Navigation Features

- Persistent section selection across browser refreshes
- Loading indicators for better UX
- Progress tracking
- Previous/Next navigation
- Mobile-responsive sidebar

### Performance Optimizations

- Lazy loading of markdown content
- Efficient search indexing
- Optimized rendering
- Caching support

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Eighty Nine](https://github.com/eightynine)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
