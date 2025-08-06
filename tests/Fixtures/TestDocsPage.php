<?php

declare(strict_types=1);

namespace EightyNine\FilamentDocs\Tests\Fixtures;

use EightyNine\FilamentDocs\Pages\DocsPage;

class TestDocsPage extends DocsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Test';
    
    protected static ?string $title = 'Test Documentation';

    public function getDocsPath(): string
    {
        return __DIR__ . '/docs';
    }
}
