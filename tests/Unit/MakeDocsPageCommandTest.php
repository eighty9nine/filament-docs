<?php

use EightyNine\FilamentDocs\Commands\MakeDocsPageCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->filesystem = new Filesystem();
    $this->testPath = storage_path('framework/testing/docs');
    
    // Clean up before each test
    if ($this->filesystem->exists($this->testPath)) {
        $this->filesystem->deleteDirectory($this->testPath);
    }
});

afterEach(function () {
    // Clean up after each test
    if ($this->filesystem->exists($this->testPath)) {
        $this->filesystem->deleteDirectory($this->testPath);
    }
    
    // Clean up created page files
    $pagePath = app_path('Filament/Pages/TestDocs.php');
    if (file_exists($pagePath)) {
        unlink($pagePath);
    }
});

it('can create docs page with default options', function () {
    $command = new MakeDocsPageCommand();
    $command->setLaravel(app());
    
    // Mock the input/output
    $this->artisan('make:filament-docs-page', ['name' => 'TestDocs'])
        ->expectsOutput('Documentation page \'TestDocs\' created successfully!')
        ->assertExitCode(0);
});

it('can create docs page with custom options', function () {
    $this->artisan('make:filament-docs-page', [
        'name' => 'CustomDocs',
        '--panel' => 'custom',
        '--path' => $this->testPath,
        '--navigation-group' => 'Custom Group',
        '--navigation-icon' => 'heroicon-o-document'
    ])
    ->expectsOutput('Documentation page \'CustomDocs\' created successfully!')
    ->assertExitCode(0);
    
    // Check if directory was created
    expect($this->filesystem->exists($this->testPath))->toBeTrue();
    
    // Check if sample markdown was created
    expect($this->filesystem->exists($this->testPath . '/getting-started.md'))->toBeTrue();
});

it('fails when page already exists', function () {
    // Create the page first
    $this->artisan('make:filament-docs-page', ['name' => 'ExistingDocs'])
        ->assertExitCode(0);
    
    // Try to create it again
    $this->artisan('make:filament-docs-page', ['name' => 'ExistingDocs'])
        ->expectsOutput('Page ExistingDocs already exists!')
        ->assertExitCode(1);
});

it('creates page file with correct content', function () {
    $this->artisan('make:filament-docs-page', [
        'name' => 'ContentTest',
        '--path' => $this->testPath,
        '--navigation-group' => 'Test Group',
        '--navigation-icon' => 'heroicon-o-test'
    ])->assertExitCode(0);
    
    $pagePath = app_path('Filament/Pages/ContentTest.php');
    
    expect(file_exists($pagePath))->toBeTrue();
    
    $content = file_get_contents($pagePath);
    
    expect($content)->toContain('class ContentTest extends DocsPage')
        ->and($content)->toContain('Test Group')
        ->and($content)->toContain('heroicon-o-test')
        ->and($content)->toContain($this->testPath);
});

it('creates sample markdown with correct content', function () {
    $this->artisan('make:filament-docs-page', [
        'name' => 'SampleTest',
        '--path' => $this->testPath
    ])->assertExitCode(0);
    
    $markdownPath = $this->testPath . '/getting-started.md';
    
    expect(file_exists($markdownPath))->toBeTrue();
    
    $content = file_get_contents($markdownPath);
    
    expect($content)->toContain('# SampleTest')
        ->and($content)->toContain('Welcome to');
});
