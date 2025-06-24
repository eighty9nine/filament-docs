<?php

test('commands have correct namespace')
    ->expect('EightyNine\FilamentDocs\Commands')
    ->toHaveNamespace('EightyNine\FilamentDocs\Commands')
    ->and('EightyNine\FilamentDocs\Commands')
    ->toExtend(Illuminate\Console\Command::class);

test('pages have correct namespace')
    ->expect('EightyNine\FilamentDocs\Pages')
    ->toHaveNamespace('EightyNine\FilamentDocs\Pages');

test('service provider follows laravel conventions')
    ->expect('EightyNine\FilamentDocs\FilamentDocsServiceProvider')
    ->toExtend(Spatie\LaravelPackageTools\PackageServiceProvider::class)
    ->and('EightyNine\FilamentDocs\FilamentDocsServiceProvider')
    ->toHaveMethod('configurePackage');

test('docs page is abstract')
    ->expect('EightyNine\FilamentDocs\Pages\DocsPage')
    ->toBeAbstract()
    ->toExtend(Filament\Pages\Page::class);

test('plugin implements correct interface')
    ->expect('EightyNine\FilamentDocs\FilamentDocsPlugin')
    ->toImplement(Filament\Contracts\Plugin::class)
    ->toHaveMethod('getId')
    ->toHaveMethod('register')
    ->toHaveMethod('boot');

test('classes use proper dependencies')
    ->expect('EightyNine\FilamentDocs')
    ->not->toUse([
        'dd',
        'dump',
        'ray',
        'var_dump',
        'print_r',
        'echo',
        'exit',
        'die'
    ]);

test('no debugging functions in production code')
    ->expect('EightyNine\FilamentDocs')
    ->not->toUse([
        'dd',
        'dump',
        'var_dump',
        'print_r'
    ]);

test('classes follow psr-4 naming convention')
    ->expect('EightyNine\FilamentDocs')
    ->classes()
    ->toBePsr4Compliant();

test('all classes have proper visibility for methods')
    ->expect('EightyNine\FilamentDocs')
    ->classes()
    ->toHaveProtectedOrPrivateFields();

test('commands have proper signature and description')
    ->expect('EightyNine\FilamentDocs\Commands')
    ->classes()
    ->toHaveProperty('signature')
    ->toHaveProperty('description');

test('service provider methods are properly structured')
    ->expect('EightyNine\FilamentDocs\FilamentDocsServiceProvider')
    ->toHaveMethod('configurePackage')
    ->toHaveMethod('packageRegistered')
    ->toHaveMethod('packageBooted');

test('docs page has required abstract methods')
    ->expect('EightyNine\FilamentDocs\Pages\DocsPage')
    ->toHaveMethod('getDocsPath');

test('all public methods in DocsPage return proper types')
    ->expect('EightyNine\FilamentDocs\Pages\DocsPage')
    ->toHaveMethod('getManualSections')
    ->toHaveMethod('loadSectionContent')
    ->toHaveMethod('getCurrentSection')
    ->toHaveMethod('getSearchResults')
    ->toHaveMethod('getFilteredSections');

test('configuration structure is consistent')
    ->expect(fn() => app('config')->get('filament-docs'))
    ->toHaveKeys([
        'default_docs_path',
        'markdown',
        'search',
        'ui',
        'section_order',
        'supported_extensions',
        'commands'
    ]);

test('package follows laravel package conventions')
    ->expect('EightyNine\FilamentDocs\FilamentDocsServiceProvider')
    ->toHaveProperty('name')
    ->toHaveProperty('viewNamespace');

test('all classes use proper type hints')
    ->expect('EightyNine\FilamentDocs')
    ->classes()
    ->toUseStrictTypes();

test('no global variables used')
    ->expect('EightyNine\FilamentDocs')
    ->not->toUse([
        '$GLOBALS',
        '$_GET',
        '$_POST',
        '$_SESSION',
        '$_COOKIE',
        '$_SERVER',
        '$_ENV',
        '$_FILES',
        '$_REQUEST'
    ]);
