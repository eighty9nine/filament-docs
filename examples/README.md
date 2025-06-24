# Filament Docs Examples

This directory contains examples demonstrating how to use the Filament Docs package with configuration-driven CommonMark extensions.

## Files

### ExampleDocumentation.php
A sample documentation page class that demonstrates:
- Using configuration-driven docs path
- Customizing section ordering while leveraging config defaults
- Adding custom navigation badges
- Overriding default behaviors

### sample-docs/extensions-demo.md
A comprehensive markdown file showcasing:
- All available CommonMark extensions
- How extensions affect rendering
- Configuration examples
- Security features

## Quick Start

1. **Copy the example page to your application**:
   ```bash
   cp examples/ExampleDocumentation.php app/Filament/Pages/
   ```

2. **Create the documentation directory**:
   ```bash
   mkdir -p resources/docs/example
   ```

3. **Copy the sample markdown**:
   ```bash
   cp examples/sample-docs/extensions-demo.md resources/docs/example/
   ```

4. **Update your configuration** in `config/filament-docs.php`:
   ```php
   'markdown' => [
       'extensions' => [
           'table' => true,
           'strikethrough' => true,
           'autolink' => true,
           'task_list' => true,
           'footnote' => true,
           // ... enable desired extensions
       ],
   ],
   ```

5. **Install optional CommonMark extensions** (composer packages):
   ```bash
   composer require league/commonmark-ext-table
   composer require league/commonmark-ext-strikethrough
   composer require league/commonmark-ext-autolink
   composer require league/commonmark-ext-task-list
   # ... install other extensions as needed
   ```

## Available Extensions

The configuration supports the following CommonMark extensions:

| Extension | Package | Description |
|-----------|---------|-------------|
| `table` | `league/commonmark-ext-table` | GitHub-style tables |
| `strikethrough` | `league/commonmark-ext-strikethrough` | ~~Strikethrough text~~ |
| `autolink` | `league/commonmark-ext-autolink` | Automatic URL detection |
| `task_list` | `league/commonmark-ext-task-list` | - [x] Task lists |
| `footnote` | `league/commonmark-ext-footnote` | Footnote support[^1] |
| `description_list` | `league/commonmark-ext-description-list` | Definition lists |
| `external_link` | `league/commonmark-ext-external-link` | External link handling |
| `table_of_contents` | `league/commonmark-ext-table-of-contents` | Auto-generated TOC |
| `smart_punct` | `league/commonmark-ext-smart-punct` | Smart punctuation |
| `heading_permalink` | `league/commonmark-ext-heading-permalink` | Heading anchors |
| `attributes` | `league/commonmark-ext-attributes` | HTML attributes in markdown |
| `disallowed_raw_html` | Built-in | Security: filter dangerous HTML |

[^1]: Like this footnote!

## Configuration Examples

### Basic Configuration
```php
'markdown' => [
    'extensions' => [
        'commonmark_core' => true,
        'table' => true,
        'strikethrough' => true,
        'autolink' => true,
    ],
],
```

### Advanced Configuration with Options
```php
'markdown' => [
    'extensions' => [
        'external_link' => [
            'internal_hosts' => ['localhost', 'yourdomain.com'],
            'open_in_new_window' => true,
            'html_class' => 'external-link',
            'nofollow' => 'external',
        ],
        'heading_permalink' => [
            'html_class' => 'heading-permalink',
            'symbol' => '🔗',
            'title' => 'Direct link to this section',
        ],
        'disallowed_raw_html' => [
            'disallowed_tags' => ['script', 'iframe', 'object', 'embed'],
        ],
    ],
],
```

### Security-Focused Configuration
```php
'markdown' => [
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
    'extensions' => [
        'disallowed_raw_html' => [
            'disallowed_tags' => [
                'script', 'iframe', 'object', 'embed', 'form',
                'input', 'button', 'select', 'textarea'
            ],
        ],
        // Enable safe extensions only
        'table' => true,
        'task_list' => true,
        'autolink' => true,
    ],
],
```

## Tips

1. **Performance**: Only enable extensions you actually use
2. **Security**: Always use `disallowed_raw_html` in production
3. **Testing**: Test with sample content to verify extensions work as expected
4. **Documentation**: Document which extensions are available for your content creators

## Troubleshooting

### Extension Not Working
1. Check if the extension package is installed via Composer
2. Verify the extension is enabled in configuration
3. Check for any PHP errors in logs
4. Ensure the markdown syntax is correct

### Performance Issues
1. Disable unused extensions
2. Use caching for large documentation sets
3. Consider lazy loading for very large documents

### Security Concerns
1. Always enable `disallowed_raw_html` extension
2. Set `html_input` to `'strip'` or `'escape'`
3. Set `allow_unsafe_links` to `false`
4. Regularly review and update security settings
