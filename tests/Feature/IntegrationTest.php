<?php

use EightyNine\FilamentDocs\FilamentDocsServiceProvider;
use EightyNine\FilamentDocs\Commands\MakeDocsPageCommand;
use EightyNine\FilamentDocs\Commands\MakeMarkdownCommand;
use Illuminate\Filesystem\Filesystem;

it('can boot the service provider without errors', function () {
    $provider = new FilamentDocsServiceProvider(app());
    
    // This should not throw any exceptions
    $provider->packageBooted();
    
    expect(true)->toBeTrue();
});

it('registers artisan commands correctly', function () {
    $commands = collect(app('Illuminate\Contracts\Console\Kernel')->all())->keys();
    
    expect($commands)->toContain('make:filament-docs-page')
        ->and($commands)->toContain('make:filament-docs-markdown');
});

it('can create a complete documentation workflow', function () {
    $testPath = storage_path('framework/testing/integration');
    $filesystem = new Filesystem();
    
    // Clean up
    if ($filesystem->exists($testPath)) {
        $filesystem->deleteDirectory($testPath);
    }
    
    // Step 1: Create a docs page
    $this->artisan('make:filament-docs-page', [
        'name' => 'IntegrationDocs',
        '--path' => $testPath,
        '--navigation-group' => 'Test',
        '--navigation-icon' => 'heroicon-o-document'
    ])->assertExitCode(0);
    
    // Step 2: Create additional markdown files
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Configuration Guide',
        '--path' => $testPath,
        '--template' => 'guide'
    ])->assertExitCode(0);
    
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'API Reference',
        '--path' => $testPath,
        '--template' => 'api'
    ])->assertExitCode(0);
    
    // Verify files were created
    expect(file_exists(app_path('Filament/Pages/IntegrationDocs.php')))->toBeTrue();
    expect(file_exists($testPath . '/getting-started.md'))->toBeTrue();
    expect(file_exists($testPath . '/configuration-guide.md'))->toBeTrue();
    expect(file_exists($testPath . '/api-reference.md'))->toBeTrue();
    
    // Step 3: Test the docs page functionality
    $pageContent = file_get_contents(app_path('Filament/Pages/IntegrationDocs.php'));
    expect($pageContent)->toContain('class IntegrationDocs extends DocsPage')
        ->and($pageContent)->toContain($testPath);
    
    // Clean up
    if ($filesystem->exists($testPath)) {
        $filesystem->deleteDirectory($testPath);
    }
    if (file_exists(app_path('Filament/Pages/IntegrationDocs.php'))) {
        unlink(app_path('Filament/Pages/IntegrationDocs.php'));
    }
});

it('can handle multiple documentation sections', function () {
    $testPath = storage_path('framework/testing/multi-docs');
    $filesystem = new Filesystem();
    
    // Clean up
    if ($filesystem->exists($testPath)) {
        $filesystem->deleteDirectory($testPath);
    }
    
    // Create multiple markdown files
    $files = [
        ['name' => 'Getting Started', 'template' => 'basic'],
        ['name' => 'Installation', 'template' => 'guide'],
        ['name' => 'Configuration', 'template' => 'guide'],
        ['name' => 'Usage Examples', 'template' => 'feature'],
        ['name' => 'API Documentation', 'template' => 'api'],
        ['name' => 'Troubleshooting', 'template' => 'troubleshooting'],
    ];
    
    foreach ($files as $file) {
        $this->artisan('make:filament-docs-markdown', [
            'name' => $file['name'],
            '--path' => $testPath,
            '--template' => $file['template']
        ])->assertExitCode(0);
    }
    
    // Verify all files were created
    expect($filesystem->exists($testPath))->toBeTrue();
    
    $createdFiles = $filesystem->files($testPath);
    expect(count($createdFiles))->toBe(6);
    
    // Clean up
    if ($filesystem->exists($testPath)) {
        $filesystem->deleteDirectory($testPath);
    }
});

it('integrates properly with laravel service container', function () {
    // Check if the config is loaded (indicates service provider is working)
    expect(config('filament-docs'))->not->toBeNull()
        ->and(config('filament-docs.default_docs_path'))->toBeString();
});

it('can load configuration from published config file', function () {
    // Test that configuration is loaded
    $config = $this->app['config']->get('filament-docs');
    
    expect($config)->not->toBeNull()
        ->and($config)->toBeArray()
        ->and($config)->toHaveKey('default_docs_path')
        ->and($config)->toHaveKey('markdown')
        ->and($config)->toHaveKey('search')
        ->and($config)->toHaveKey('ui');
});

it('can handle package asset registration', function () {
    $provider = new FilamentDocsServiceProvider(app());
    
    // Call packageBooted to register assets
    $provider->packageBooted();
    
    // This should complete without errors
    expect(true)->toBeTrue();
});

it('maintains consistent behavior across different php versions', function () {
    // Test basic functionality that should work across PHP versions
    $docsPage = new class extends \EightyNine\FilamentDocs\Pages\DocsPage {
        protected static ?string $title = 'Version Test';
        
        protected function getDocsPath(): string
        {
            return __DIR__ . '/../Fixtures/docs';
        }
    };
    
    $sections = $docsPage->getManualSections();
    $currentSection = $docsPage->getCurrentSection();
    
    expect($sections)->toBeArray();
    expect($currentSection)->toBeArray();
});

it('can handle edge cases in file processing', function () {
    // Test with empty directory
    $emptyPath = storage_path('framework/testing/empty');
    $filesystem = new Filesystem();
    
    if ($filesystem->exists($emptyPath)) {
        $filesystem->deleteDirectory($emptyPath);
    }
    
    $filesystem->makeDirectory($emptyPath, 0755, true);
    
    $docsPage = new class($emptyPath) extends \EightyNine\FilamentDocs\Pages\DocsPage {
        private string $path;
        
        public function __construct(string $path)
        {
            $this->path = $path;
        }
        
        protected static ?string $title = 'Empty Test';
        
        protected function getDocsPath(): string
        {
            return $this->path;
        }
    };
    
    $sections = $docsPage->getManualSections();
    expect($sections)->toBeArray()->toBeEmpty();
    
    // Clean up
    if ($filesystem->exists($emptyPath)) {
        $filesystem->deleteDirectory($emptyPath);
    }
});

it('provides proper error handling for missing files', function () {
    $docsPage = new class extends \EightyNine\FilamentDocs\Pages\DocsPage {
        protected static ?string $title = 'Missing Test';
        
        protected function getDocsPath(): string
        {
            return '/completely/nonexistent/path';
        }
    };
    
    $sections = $docsPage->getManualSections();
    $currentSection = $docsPage->getCurrentSection();
    
    expect($sections)->toBeArray()->toBeEmpty();
    expect($currentSection)->toBeNull();
});
