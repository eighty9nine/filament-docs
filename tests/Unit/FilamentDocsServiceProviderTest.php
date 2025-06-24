<?php

use EightyNine\FilamentDocs\FilamentDocsServiceProvider;

it('can register the service provider', function () {
    $provider = new FilamentDocsServiceProvider(app());
    
    expect($provider)->toBeInstanceOf(FilamentDocsServiceProvider::class);
});

it('has correct package name', function () {
    expect(FilamentDocsServiceProvider::$name)->toBe('filament-docs');
});

it('has correct view namespace', function () {
    expect(FilamentDocsServiceProvider::$viewNamespace)->toBe('filament-docs');
});

it('registers commands', function () {
    $provider = new FilamentDocsServiceProvider(app());
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getCommands');
    $method->setAccessible(true);
    
    $commands = $method->invoke($provider);
    
    expect($commands)->toContain(
        \EightyNine\FilamentDocs\Commands\MakeDocsPageCommand::class,
        \EightyNine\FilamentDocs\Commands\MakeMarkdownCommand::class
    );
});

it('registers assets', function () {
    $provider = new FilamentDocsServiceProvider(app());
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getAssets');
    $method->setAccessible(true);
    
    $assets = $method->invoke($provider);
    
    expect($assets)->toHaveCount(2);
});

it('has correct asset package name', function () {
    $provider = new FilamentDocsServiceProvider(app());
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getAssetPackageName');
    $method->setAccessible(true);
    
    $packageName = $method->invoke($provider);
    
    expect($packageName)->toBe('eightynine/filament-docs');
});
