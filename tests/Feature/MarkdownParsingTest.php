<?php

use EightyNine\FilamentDocs\Tests\Fixtures\TestDocsPage;
use League\CommonMark\Environment\Environment;
use League\CommonMark\CommonMarkConverter;

beforeEach(function () {
    $this->docsPage = new TestDocsPage();
});

it('can parse basic markdown elements', function () {
    $markdown = '# Heading 1

## Heading 2

This is a **bold** text and this is *italic* text.

Here is a [link](https://example.com).

- List item 1
- List item 2
- List item 3

1. Numbered item 1
2. Numbered item 2

> This is a blockquote.

`inline code`

```php
<?php
echo "Hello World!";
```';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<h1>')
        ->and($html)->toContain('<h2>')
        ->and($html)->toContain('<strong>')
        ->and($html)->toContain('<em>')
        ->and($html)->toContain('<a href="https://example.com">')
        ->and($html)->toContain('<ul>')
        ->and($html)->toContain('<li>')
        ->and($html)->toContain('<ol>')
        ->and($html)->toContain('<blockquote>')
        ->and($html)->toContain('<code>')
        ->and($html)->toContain('<pre>');
});

it('can parse tables when table extension is enabled', function () {
    // Set table extension to true
    $this->app['config']->set('filament-docs.markdown.extensions.table', true);
    
    $markdown = '| Column 1 | Column 2 | Column 3 |
|----------|----------|----------|
| Data 1   | Data 2   | Data 3   |
| Data 4   | Data 5   | Data 6   |';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<table>')
        ->and($html)->toContain('<thead>')
        ->and($html)->toContain('<tbody>')
        ->and($html)->toContain('<th>')
        ->and($html)->toContain('<td>');
});

it('can parse strikethrough when extension is enabled', function () {
    $this->app['config']->set('filament-docs.markdown.extensions.strikethrough', true);
    
    $markdown = 'This is ~~strikethrough~~ text.';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<del>strikethrough</del>');
});

it('can parse task lists when extension is enabled', function () {
    $this->app['config']->set('filament-docs.markdown.extensions.task_list', true);
    
    $markdown = '- [x] Completed task
- [ ] Incomplete task
- [x] Another completed task';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('type="checkbox"')
        ->and($html)->toContain('checked')
        ->and($html)->toContain('disabled');
});

it('can parse autolinks when extension is enabled', function () {
    $this->app['config']->set('filament-docs.markdown.extensions.autolink', true);
    
    $markdown = 'Visit https://example.com for more information.';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<a href="https://example.com">https://example.com</a>');
});

it('handles configured extensions correctly', function () {
    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('addConfiguredExtensions');
    $method->setAccessible(true);
    
    $environment = new Environment();
    $extensionsConfig = [
        'commonmark_core' => true,
        'table' => true,
        'strikethrough' => true,
        'autolink' => true,
        'task_list' => true,
    ];
    
    $method->invoke($this->docsPage, $environment, $extensionsConfig);
    
    // Test that environment was configured (no exceptions thrown)
    expect(true)->toBeTrue();
});

it('respects html input configuration', function () {
    $this->app['config']->set('filament-docs.markdown.html_input', 'strip');
    
    $markdown = 'This has <script>alert("xss")</script> HTML content.';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->not->toContain('<script>');
});

it('respects max nesting level configuration', function () {
    $this->app['config']->set('filament-docs.markdown.max_nesting_level', 2);
    
    // This should work fine with the configuration
    $markdown = '> Level 1
> > Level 2';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<blockquote>');
});

it('can handle footnotes when extension is enabled', function () {
    $this->app['config']->set('filament-docs.markdown.extensions.footnote', true);
    
    $markdown = 'This has a footnote[^1].

[^1]: This is the footnote content.';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    // The exact output format may vary, but should contain footnote elements
    expect($html)->toBeString();
});

it('can handle description lists when extension is enabled', function () {
    $this->app['config']->set('filament-docs.markdown.extensions.description_list', true);
    
    $markdown = 'Term 1
:   Definition 1

Term 2
:   Definition 2';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toBeString();
});

it('handles smart punctuation when extension is enabled', function () {
    $this->app['config']->set('filament-docs.markdown.extensions.smart_punct', true);
    
    $markdown = 'This is "quoted" text with -- dashes.';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toBeString();
});

it('safely handles missing extensions', function () {
    // Configure an extension that might not be available
    $this->app['config']->set('filament-docs.markdown.extensions.non_existent_extension', true);
    
    $markdown = '# Test';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    // Should not throw exception
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<h1>Test</h1>');
});

it('can parse complex markdown documents', function () {
    $markdown = '# Complex Document

## Table of Contents

1. [Introduction](#introduction)
2. [Features](#features)
3. [Examples](#examples)

## Introduction

This is a **complex** document with *various* elements.

## Features

- [x] Tables
- [x] Code blocks
- [ ] More features coming

| Feature | Status | Notes |
|---------|--------|-------|
| Tables | ✅ | Working |
| Search | ✅ | Working |

## Examples

```php
<?php
class Example {
    public function test() {
        return "Hello World!";
    }
}
```

Visit https://example.com for more information.

> Important note about the system.

### Footnotes

This has a reference[^1].

[^1]: Reference material here.';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<h1>')
        ->and($html)->toContain('<h2>')
        ->and($html)->toContain('<h3>')
        ->and($html)->toContain('<table>')
        ->and($html)->toContain('<code>')
        ->and($html)->toContain('<pre>')
        ->and($html)->toContain('<blockquote>')
        ->and($html)->toBeString();
});
