<?php

use EightyNine\FilamentDocs\Commands\MakeMarkdownCommand;
use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->filesystem = new Filesystem();
    $this->testPath = storage_path('framework/testing/markdown');
    
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
});

it('can create basic markdown file', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Test Document',
        '--path' => $this->testPath
    ])
    ->expectsOutput('Markdown file \'test-document.md\' created successfully!')
    ->assertExitCode(0);
    
    $filePath = $this->testPath . '/test-document.md';
    expect(file_exists($filePath))->toBeTrue();
    
    $content = file_get_contents($filePath);
    expect($content)->toContain('# Test Document');
});

it('can create guide template markdown', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Guide Document',
        '--path' => $this->testPath,
        '--template' => 'guide'
    ])->assertExitCode(0);
    
    $filePath = $this->testPath . '/guide-document.md';
    expect(file_exists($filePath))->toBeTrue();
    
    $content = file_get_contents($filePath);
    expect($content)->toContain('# Guide Document')
        ->and($content)->toContain('## Prerequisites')
        ->and($content)->toContain('## Step-by-Step Instructions');
});

it('can create api template markdown', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'API Reference',
        '--path' => $this->testPath,
        '--template' => 'api'
    ])->assertExitCode(0);
    
    $filePath = $this->testPath . '/api-reference.md';
    expect(file_exists($filePath))->toBeTrue();
    
    $content = file_get_contents($filePath);
    expect($content)->toContain('# API Reference')
        ->and($content)->toContain('## Endpoints')
        ->and($content)->toContain('## Authentication')
        ->and($content)->toContain('## Parameters');
});

it('can create troubleshooting template markdown', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Troubleshooting',
        '--path' => $this->testPath,
        '--template' => 'troubleshooting'
    ])->assertExitCode(0);
    
    $filePath = $this->testPath . '/troubleshooting.md';
    expect(file_exists($filePath))->toBeTrue();
    
    $content = file_get_contents($filePath);
    expect($content)->toContain('# Troubleshooting')
        ->and($content)->toContain('## Common Issues')
        ->and($content)->toContain('## FAQ');
});

it('can create feature template markdown', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Feature Overview',
        '--path' => $this->testPath,
        '--template' => 'feature'
    ])->assertExitCode(0);
    
    $filePath = $this->testPath . '/feature-overview.md';
    expect(file_exists($filePath))->toBeTrue();
    
    $content = file_get_contents($filePath);
    expect($content)->toContain('# Feature Overview')
        ->and($content)->toContain('## Description')
        ->and($content)->toContain('## Usage');
});

it('fails when markdown file already exists', function () {
    // Create the file first
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Existing File',
        '--path' => $this->testPath
    ])->assertExitCode(0);
    
    // Try to create it again
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Existing File',
        '--path' => $this->testPath
    ])
    ->expectsOutput('Markdown file \'existing-file.md\' already exists!')
    ->assertExitCode(1);
});

it('creates directory if it does not exist', function () {
    $nonExistentPath = $this->testPath . '/new/subdirectory';
    
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'New Document',
        '--path' => $nonExistentPath
    ])->assertExitCode(0);
    
    expect($this->filesystem->exists($nonExistentPath))->toBeTrue();
    expect(file_exists($nonExistentPath . '/new-document.md'))->toBeTrue();
});

it('uses default path when no path provided', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Default Path Test'
    ])->assertExitCode(0);
    
    $defaultPath = resource_path('docs');
    expect(file_exists($defaultPath . '/default-path-test.md'))->toBeTrue();
    
    // Clean up
    if (file_exists($defaultPath . '/default-path-test.md')) {
        unlink($defaultPath . '/default-path-test.md');
    }
});

it('uses basic template when invalid template provided', function () {
    $this->artisan('make:filament-docs-markdown', [
        'name' => 'Invalid Template Test',
        '--path' => $this->testPath,
        '--template' => 'invalid-template'
    ])->assertExitCode(0);
    
    $filePath = $this->testPath . '/invalid-template-test.md';
    expect(file_exists($filePath))->toBeTrue();
    
    $content = file_get_contents($filePath);
    expect($content)->toContain('# Invalid Template Test')
        ->and($content)->toContain('Welcome to the Invalid Template Test documentation');
});
