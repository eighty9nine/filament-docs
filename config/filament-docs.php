<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Documentation Path
    |--------------------------------------------------------------------------
    |
    | This option defines the default path where markdown documentation
    | files will be stored. You can override this in your DocsPage
    | implementation by overriding the getDocsPath() method.
    |
    */

    'default_docs_path' => resource_path('docs'),

    /*
    |--------------------------------------------------------------------------
    | Markdown Parser Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the CommonMark markdown parser.
    | These settings will be passed to the CommonMarkConverter.
    |
    */

    'markdown' => [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 10,
        'slug_normalizer' => [
            'max_length' => 255,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the search functionality.
    |
    */

    'search' => [
        'debounce_ms' => 300,
        'max_results_per_section' => 3,
        'highlight_class' => 'bg-yellow-200 text-yellow-800 px-1 rounded',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the user interface.
    |
    */

    'ui' => [
        'sidebar_width' => 'lg:w-80',
        'max_sidebar_height' => 'max-h-96',
        'loading_delay_ms' => 300,
        'default_navigation_icon' => 'heroicon-o-book-open',
        'default_navigation_group' => 'Documentation',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Ordering
    |--------------------------------------------------------------------------
    |
    | Default ordering for documentation sections. You can override this
    | in your DocsPage implementation by overriding the getSectionOrder() method.
    |
    */

    'section_order' => [
        'getting-started' => 1,
        'installation' => 2,
        'configuration' => 3,
        'usage' => 4,
        'examples' => 5,
        'api' => 6,
        'api-reference' => 7,
        'troubleshooting' => 8,
        'faq' => 9,
        'changelog' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Extensions
    |--------------------------------------------------------------------------
    |
    | Supported file extensions for documentation files.
    |
    */

    'supported_extensions' => ['md', 'markdown'],

    /*
    |--------------------------------------------------------------------------
    | Commands Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the package commands.
    |
    */

    'commands' => [
        'make_docs_page' => [
            'default_panel' => 'admin',
            'default_navigation_group' => 'Documentation',
            'default_navigation_icon' => 'heroicon-o-book-open',
        ],
        'make_markdown' => [
            'templates' => ['basic', 'guide', 'api', 'troubleshooting', 'feature'],
            'default_template' => 'basic',
        ],
    ],

];
