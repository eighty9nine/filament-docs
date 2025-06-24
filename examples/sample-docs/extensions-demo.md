# Example Documentation with Extensions

This document demonstrates various CommonMark extensions that can be enabled through the `filament-docs.php` configuration file.

## Tables (Table Extension)

| Feature | Status | Notes |
|---------|--------|-------|
| Tables | ✅ Enabled | GitHub-style tables |
| Strikethrough | ✅ Enabled | ~~Crossed out text~~ |
| Autolinks | ✅ Enabled | Automatic URL detection |
| Task Lists | ✅ Enabled | Interactive checkboxes |

## Text Formatting

### Strikethrough Extension
~~This text has been struck through~~

### Smart Punctuation Extension
- "Smart quotes" are automatically converted
- Dashes -- become em-dashes
- Three dots... become ellipsis

## Interactive Elements

### Task Lists (TaskList Extension)
- [x] Completed task
- [ ] Pending task
- [ ] Another pending task
  - [x] Nested completed task
  - [ ] Nested pending task

## Links and References

### Autolink Extension
Automatic link detection: https://github.com/eightynine/filament-docs

### External Links Extension
[External Link](https://example.com) - Opens in new window with proper attributes

[Internal Link](./another-section.md) - Stays in same window

## Code and Technical Content

### Code Blocks with Syntax Highlighting
```php
<?php

class ExampleClass
{
    public function demonstrateFeature(): string
    {
        return 'This code block supports syntax highlighting';
    }
}
```

```javascript
// JavaScript example
function demonstrateExtensions() {
    console.log('Extensions are configurable!');
    return true;
}
```

## Advanced Features

### Footnotes (Footnote Extension)
This is a sentence with a footnote[^1].

Here's another footnote reference[^note].

[^1]: This is the first footnote.
[^note]: This is a named footnote.

### Description Lists (DescriptionList Extension)
Apple
: A red fruit

Orange
: An orange fruit

Banana
: A yellow fruit

### Heading Permalinks (HeadingPermalink Extension)
Each heading automatically gets a permalink anchor that you can link to directly.

## Attributes Extension Example

If attributes extension is enabled, you can add HTML attributes:

This is a paragraph with custom attributes.
{.custom-class #custom-id}

## Table of Contents

If the TableOfContents extension is enabled, a TOC can be automatically generated at a specified position.

## Security Features

### Disallowed Raw HTML
The DisallowedRawHtml extension prevents potentially dangerous HTML:

- `<script>` tags are automatically removed
- `<iframe>` tags are blocked
- `<object>` and `<embed>` tags are filtered
- `<form>` tags are stripped

This ensures your documentation remains secure even if contributors accidentally include unsafe HTML.

## Configuration Example

To enable these extensions, update your `config/filament-docs.php`:

```php
'markdown' => [
    'extensions' => [
        'table' => true,
        'strikethrough' => true,
        'autolink' => true,
        'task_list' => true,
        'footnote' => true,
        'description_list' => true,
        'smart_punct' => true,
        'disallowed_raw_html' => [
            'disallowed_tags' => ['script', 'iframe', 'object', 'embed', 'form'],
        ],
        'external_link' => [
            'open_in_new_window' => true,
            'html_class' => 'external-link',
        ],
        'heading_permalink' => [
            'symbol' => '#',
            'title' => 'Permalink',
        ],
    ],
],
```

## Benefits of Configuration-Driven Extensions

1. **Flexibility**: Enable only the extensions you need
2. **Performance**: Avoid loading unnecessary extensions
3. **Security**: Configure security settings appropriately
4. **Consistency**: Same configuration across all documentation pages
5. **Maintainability**: Central configuration management

---

*This example demonstrates how the DocsPage class now loads CommonMark extensions from the configuration file, making it easy to customize markdown processing behavior.*
