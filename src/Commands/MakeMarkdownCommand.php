<?php

namespace EightyNine\FilamentDocs\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeMarkdownCommand extends Command
{
    public $signature = 'make:filament-docs-markdown {name} {--path=} {--template=}';

    public $description = 'Create a new markdown documentation file';

    public function handle(): int
    {
        $name = $this->argument('name');
        $path = $this->option('path') ?? resource_path('docs');
        $template = $this->option('template') ?? 'basic';

        $filename = Str::slug($name) . '.md';
        $filepath = "{$path}/{$filename}";

        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists($path);

        if (file_exists($filepath)) {
            $this->error("Markdown file '{$filename}' already exists!");
            return self::FAILURE;
        }

        $content = $this->getMarkdownTemplate($template, $name);
        file_put_contents($filepath, $content);

        $this->info("Markdown file '{$filename}' created successfully!");
        $this->info("File location: {$filepath}");

        return self::SUCCESS;
    }

    protected function getMarkdownTemplate(string $template, string $name): string
    {
        return match ($template) {
            'guide' => $this->getGuideTemplate($name),
            'api' => $this->getApiTemplate($name),
            'troubleshooting' => $this->getTroubleshootingTemplate($name),
            'feature' => $this->getFeatureTemplate($name),
            default => $this->getBasicTemplate($name),
        };
    }

    protected function getBasicTemplate(string $name): string
    {
        return <<<MD
# {$name}

## Overview

Brief description of {$name}.

## Content

Add your documentation content here.

## Examples

```bash
# Example command
echo "Hello World"
```

## Notes

- Important note 1
- Important note 2

---

*Last updated: $(date)*
MD;
    }

    protected function getGuideTemplate(string $name): string
    {
        return <<<MD
# {$name} Guide

## Introduction

Welcome to the {$name} guide. This document will walk you through everything you need to know.

## Prerequisites

Before you begin, make sure you have:

- Requirement 1
- Requirement 2
- Requirement 3

## Step-by-Step Instructions

### Step 1: Initial Setup

Describe the first step in detail.

### Step 2: Configuration

Explain the configuration process.

### Step 3: Implementation

Detail the implementation steps.

## Examples

### Basic Example

```php
<?php
// Example code
echo "Hello World";
```

### Advanced Example

```php
<?php
// More complex example
class Example {
    public function method() {
        return "Advanced example";
    }
}
```

## Troubleshooting

### Common Issues

#### Issue 1
**Problem**: Description of the problem.
**Solution**: How to solve it.

#### Issue 2
**Problem**: Another common problem.
**Solution**: The solution for this issue.

## Best Practices

1. Best practice 1
2. Best practice 2
3. Best practice 3

## Related Documentation

- [Related Guide 1](#)
- [Related Guide 2](#)
- [API Reference](#)

---

*For additional help, contact support.*
MD;
    }

    protected function getApiTemplate(string $name): string
    {
        return <<<MD
# {$name} API Reference

## Overview

API documentation for {$name}.

## Authentication

Describe authentication requirements.

## Endpoints

### GET /api/endpoint

**Description**: Brief description of the endpoint.

**Parameters**:
- `param1` (string, required): Description
- `param2` (integer, optional): Description

**Response**:
```json
{
    "status": "success",
    "data": {
        "example": "value"
    }
}
```

### POST /api/endpoint

**Description**: Brief description of the POST endpoint.

**Request Body**:
```json
{
    "field1": "value1",
    "field2": "value2"
}
```

**Response**:
```json
{
    "status": "success",
    "message": "Resource created successfully"
}
```

## Error Handling

### Error Codes

- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `500`: Internal Server Error

### Error Response Format

```json
{
    "status": "error",
    "message": "Error description",
    "code": 400
}
```

## Rate Limiting

Describe rate limiting policies.

## Examples

### cURL Examples

```bash
# GET request
curl -X GET "https://api.example.com/endpoint" \
     -H "Authorization: Bearer YOUR_TOKEN"

# POST request
curl -X POST "https://api.example.com/endpoint" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"field1": "value1"}'
```

---

*API Version: 1.0*
MD;
    }

    protected function getTroubleshootingTemplate(string $name): string
    {
        return <<<MD
# {$name} Troubleshooting

## Common Issues

### Issue 1: Problem Description

**Symptoms**:
- Symptom 1
- Symptom 2
- Symptom 3

**Possible Causes**:
- Cause 1
- Cause 2

**Solutions**:
1. Solution step 1
2. Solution step 2
3. Solution step 3

**Verification**:
How to verify the issue is resolved.

### Issue 2: Another Problem

**Symptoms**:
- Different symptoms

**Solutions**:
1. Different solution steps

## Diagnostic Steps

### Step 1: Check System Status

```bash
# Commands to check system status
systemctl status service-name
```

### Step 2: Review Logs

```bash
# Commands to check logs
tail -f /path/to/logfile
```

### Step 3: Test Configuration

```bash
# Commands to test configuration
config-test --verify
```

## Prevention

### Best Practices

1. Preventive measure 1
2. Preventive measure 2
3. Preventive measure 3

### Monitoring

- Monitor metric 1
- Monitor metric 2
- Set up alerts for critical issues

## Getting Help

If you continue to experience issues:

1. Check the FAQ section
2. Search existing documentation
3. Contact support with detailed information

### Information to Include

When contacting support, please include:

- System version
- Error messages
- Steps to reproduce
- Environment details

---

*For urgent issues, contact emergency support.*
MD;
    }

    protected function getFeatureTemplate(string $name): string
    {
        return <<<MD
# {$name} Feature

## Overview

Description of the {$name} feature and its purpose.

## Benefits

- Benefit 1
- Benefit 2
- Benefit 3

## How It Works

Explain how the feature works internally.

## Configuration

### Basic Configuration

```php
<?php
// Basic configuration example
'feature' => [
    'enabled' => true,
    'option1' => 'value1',
    'option2' => 'value2',
];
```

### Advanced Configuration

```php
<?php
// Advanced configuration options
'feature' => [
    'enabled' => true,
    'advanced_options' => [
        'setting1' => 'advanced_value1',
        'setting2' => 'advanced_value2',
    ],
];
```

## Usage Examples

### Basic Usage

```php
<?php
// Basic usage example
\$feature = new Feature();
\$result = \$feature->execute();
```

### Advanced Usage

```php
<?php
// Advanced usage with options
\$feature = new Feature([
    'option1' => 'custom_value',
    'option2' => true,
]);

\$result = \$feature->execute();
```

## Integration

### With Other Features

Explain how this feature integrates with other system features.

### Third-party Integration

Describe any third-party integrations available.

## Limitations

- Limitation 1
- Limitation 2
- Limitation 3

## Performance Considerations

- Performance note 1
- Performance note 2
- Optimization tips

## Troubleshooting

### Common Issues

See the [Troubleshooting Guide](#) for common issues and solutions.

---

*Feature available since version X.X.X*
MD;
    }
}
