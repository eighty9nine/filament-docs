<?php

use EightyNine\FilamentDocs\FilamentDocsPlugin;
use Filament\Panel;

it('can create plugin instance', function () {
    $plugin = FilamentDocsPlugin::make();
    
    expect($plugin)->toBeInstanceOf(FilamentDocsPlugin::class);
});

it('has correct plugin id', function () {
    $plugin = new FilamentDocsPlugin();
    
    expect($plugin->getId())->toBe('filament-docs');
});

it('can register with panel', function () {
    $plugin = new FilamentDocsPlugin();
    $panel = Panel::make();
    
    // Should not throw exception
    $plugin->register($panel);
    
    expect(true)->toBeTrue();
});

it('can boot with panel', function () {
    $plugin = new FilamentDocsPlugin();
    $panel = Panel::make();
    
    // Should not throw exception
    $plugin->boot($panel);
    
    expect(true)->toBeTrue();
});
