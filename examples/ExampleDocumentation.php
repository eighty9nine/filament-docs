<?php

namespace App\Filament\Pages;

use EightyNine\FilamentDocs\Pages\DocsPage;

class ExampleDocumentation extends DocsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Documentation';
    protected static ?string $title = 'Example Documentation';
    protected static ?string $slug = 'example-docs';

    /**
     * Get the path to markdown files
     * 
     * This can be customized per page or use the default from config
     */
    protected function getDocsPath(): string
    {
        // Option 1: Use the default path from config
        return $this->getDefaultDocsPath();
        
        // Option 2: Use a custom path for this specific documentation page
        // return resource_path('docs/example');
        
        // Option 3: Use a completely custom path
        // return base_path('documentation/example');
    }

    /**
     * Example of customizing section order while still using config defaults
     */
    protected function getSectionOrder(string $filename): int
    {
        // Custom ordering for specific files
        $customOrder = [
            'introduction' => 0,  // Always first
            'conclusion' => 999,  // Always last
        ];
        
        if (isset($customOrder[$filename])) {
            return $customOrder[$filename];
        }
        
        // Fall back to parent method which uses config
        return parent::getSectionOrder($filename);
    }

    /**
     * Example of customizing the title extraction from markdown files
     */
    public function getTitle(): string
    {
        return 'Example Documentation with Config-Driven Extensions';
    }

    /**
     * Example of adding custom navigation badge
     */
    public static function getNavigationBadge(): ?string
    {
        return 'Enhanced';
    }
}
