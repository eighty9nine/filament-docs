<?php

it('loads default configuration correctly', function () {
    $config = $this->app['config']->get('filament-docs');
    
    expect($config)->toBeArray()
        ->and($config['default_docs_path'])->toBe(resource_path('docs'))
        ->and($config['supported_extensions'])->toContain('md', 'markdown')
        ->and($config['markdown'])->toBeArray()
        ->and($config['search'])->toBeArray()
        ->and($config['ui'])->toBeArray()
        ->and($config['section_order'])->toBeArray()
        ->and($config['commands'])->toBeArray();
});

it('has correct markdown configuration defaults', function () {
    $markdownConfig = $this->app['config']->get('filament-docs.markdown');
    
    expect($markdownConfig['html_input'])->toBe('strip')
        ->and($markdownConfig['allow_unsafe_links'])->toBeFalse()
        ->and($markdownConfig['max_nesting_level'])->toBe(10)
        ->and($markdownConfig['extensions'])->toBeArray();
});

it('has correct markdown extensions configuration', function () {
    $extensions = $this->app['config']->get('filament-docs.markdown.extensions');
    
    expect($extensions['commonmark_core'])->toBeTrue()
        ->and($extensions['table'])->toBeTrue()
        ->and($extensions['strikethrough'])->toBeTrue()
        ->and($extensions['autolink'])->toBeTrue()
        ->and($extensions['task_list'])->toBeTrue()
        ->and($extensions['footnote'])->toBeTrue()
        ->and($extensions['description_list'])->toBeTrue()
        ->and($extensions['smart_punct'])->toBeTrue();
});

it('has correct search configuration defaults', function () {
    $searchConfig = $this->app['config']->get('filament-docs.search');
    
    expect($searchConfig['debounce_ms'])->toBe(300)
        ->and($searchConfig['max_results_per_section'])->toBe(3)
        ->and($searchConfig['highlight_class'])->toBeString();
});

it('has correct ui configuration defaults', function () {
    $uiConfig = $this->app['config']->get('filament-docs.ui');
    
    expect($uiConfig['sidebar_width'])->toBe('lg:w-80')
        ->and($uiConfig['max_sidebar_height'])->toBe('max-h-96')
        ->and($uiConfig['loading_delay_ms'])->toBe(300)
        ->and($uiConfig['default_navigation_icon'])->toBe('heroicon-o-book-open')
        ->and($uiConfig['default_navigation_group'])->toBe('Documentation');
});

it('has correct section order configuration', function () {
    $sectionOrder = $this->app['config']->get('filament-docs.section_order');
    
    expect($sectionOrder['getting-started'])->toBe(1)
        ->and($sectionOrder['installation'])->toBe(2)
        ->and($sectionOrder['configuration'])->toBe(3)
        ->and($sectionOrder['usage'])->toBe(4)
        ->and($sectionOrder['troubleshooting'])->toBe(8);
});

it('has correct commands configuration', function () {
    $commandsConfig = $this->app['config']->get('filament-docs.commands');
    
    expect($commandsConfig['make_docs_page'])->toBeArray()
        ->and($commandsConfig['make_markdown'])->toBeArray()
        ->and($commandsConfig['make_docs_page']['default_panel'])->toBe('admin')
        ->and($commandsConfig['make_markdown']['default_template'])->toBe('basic');
});

it('can override configuration values', function () {
    $this->app['config']->set('filament-docs.default_docs_path', '/custom/path');
    
    expect($this->app['config']->get('filament-docs.default_docs_path'))->toBe('/custom/path');
});

it('supports all configured file extensions', function () {
    $extensions = $this->app['config']->get('filament-docs.supported_extensions');
    
    expect($extensions)->toContain('md')
        ->and($extensions)->toContain('markdown');
});

it('has proper external link configuration', function () {
    $externalLink = $this->app['config']->get('filament-docs.markdown.extensions.external_link');
    
    expect($externalLink)->toBeArray()
        ->and($externalLink['internal_hosts'])->toContain('localhost')
        ->and($externalLink['open_in_new_window'])->toBeTrue()
        ->and($externalLink['html_class'])->toBe('external-link')
        ->and($externalLink['nofollow'])->toBe('external');
});

it('has proper table of contents configuration', function () {
    $toc = $this->app['config']->get('filament-docs.markdown.extensions.table_of_contents');
    
    expect($toc)->toBeArray()
        ->and($toc['html_class'])->toBe('table-of-contents')
        ->and($toc['position'])->toBe('top')
        ->and($toc['style'])->toBe('bullet')
        ->and($toc['min_heading_level'])->toBe(1)
        ->and($toc['max_heading_level'])->toBe(6);
});

it('has proper heading permalink configuration', function () {
    $headingPermalink = $this->app['config']->get('filament-docs.markdown.extensions.heading_permalink');
    
    expect($headingPermalink)->toBeArray()
        ->and($headingPermalink['html_class'])->toBe('heading-permalink')
        ->and($headingPermalink['symbol'])->toBe('#')
        ->and($headingPermalink['aria_hidden'])->toBeTrue()
        ->and($headingPermalink['apply_id_to_heading'])->toBeTrue();
});

it('has proper disallowed raw html configuration', function () {
    $disallowedHtml = $this->app['config']->get('filament-docs.markdown.extensions.disallowed_raw_html');
    
    expect($disallowedHtml)->toBeArray()
        ->and($disallowedHtml['disallowed_tags'])->toContain('script', 'iframe', 'object', 'embed', 'form');
});
