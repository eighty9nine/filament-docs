<?php

namespace EightyNine\FilamentDocs\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeDocsPageCommand extends Command
{
    public $signature = 'make:filament-docs-page {name} {--panel=} {--path=} {--navigation-group=} {--navigation-icon=}';

    public $description = 'Create a new Filament documentation page';

    public function handle(): int
    {
        $name = $this->argument('name');
        $panel = $this->option('panel') ?? 'admin';
        $path = $this->option('path') ?? resource_path('docs');
        $navigationGroup = $this->option('navigation-group') ?? 'Documentation';
        $navigationIcon = $this->option('navigation-icon') ?? 'heroicon-o-book-open';

        $className = Str::studly($name);
        $slug = Str::slug($name);

        // Create the documentation page
        $pageContent = $this->getPageStub();
        $pageContent = str_replace(
            ['{{className}}', '{{slug}}', '{{navigationGroup}}', '{{navigationIcon}}', '{{title}}', '{{path}}'],
            [$className, $slug, $navigationGroup, $navigationIcon, $name, $path],
            $pageContent
        );

        $pagePath = app_path("Filament/Pages/{$className}.php");
        
        if (file_exists($pagePath)) {
            $this->error("Page {$className} already exists!");
            return self::FAILURE;
        }        file_put_contents($pagePath, $pageContent);

        // Create docs directory if it doesn't exist
        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists($path);

        // Create sample markdown file
        $sampleMarkdown = $this->getSampleMarkdownStub();
        $sampleMarkdown = str_replace('{{title}}', $name, $sampleMarkdown);
        file_put_contents("{$path}/getting-started.md", $sampleMarkdown);        $this->info("Documentation page '{$className}' created successfully!");
        $this->info("Page file: {$pagePath}");
        $this->info("Docs directory: {$path}");
        $this->info("Sample markdown: {$path}/getting-started.md");

        return self::SUCCESS;
    }

    protected function getPageStub(): string
    {
        return <<<'PHP'
<?php

namespace App\Filament\Pages;

use EightyNine\FilamentDocs\Pages\DocsPage;

class {{className}} extends DocsPage
{
    protected static ?string $navigationIcon = '{{navigationIcon}}';
    
    protected static ?string $navigationGroup = '{{navigationGroup}}';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $title = '{{title}}';
    
    protected static ?string $navigationLabel = '{{title}}';
    
    protected static ?string $slug = '{{slug}}';

    public function getTitle(): string
    {
        return '{{title}}';
    }

    public function getHeading(): string
    {
        return '{{title}} Documentation';
    }

    public function getSubheading(): ?string
    {
        return 'Complete guide and documentation';
    }

    /**
     * Get the path to markdown files
     */
    protected function getDocsPath(): string
    {
        return '{{path}}';
    }
}
PHP;    }

    protected function getSampleMarkdownStub(): string
    {
        return <<<'MD'
# Getting Started

Welcome to {{title}} documentation!

## Overview

This is a sample documentation page created with Filament Docs. You can edit this file to add your own content.

## Features

- **Markdown Support**: Write documentation in markdown format
- **Search Functionality**: Built-in search across all documentation
- **Navigation**: Easy sidebar navigation between sections
- **Responsive Design**: Works on desktop and mobile devices

## Usage

1. Edit the markdown files in your docs directory
2. Add new sections by creating new `.md` files
3. The system will automatically detect and display them

## Next Steps

- Create more markdown files for additional documentation sections
- Customize the navigation and styling
- Add screenshots and images to enhance your documentation

---

*Happy documenting!*
MD;
    }
}
