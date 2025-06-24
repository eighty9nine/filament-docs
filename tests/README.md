# Filament Docs Test Suite

This directory contains a comprehensive test suite for the Filament Docs package, built using Pest PHP testing framework.

## Test Structure

### Unit Tests (`tests/Unit/`)
- **DocsPageTest.php** - Tests for the main DocsPage functionality including section loading, content parsing, and search
- **FilamentDocsServiceProviderTest.php** - Tests for the service provider registration and configuration
- **FilamentDocsPluginTest.php** - Tests for the Filament plugin implementation
- **MakeDocsPageCommandTest.php** - Tests for the docs page generation command
- **MakeMarkdownCommandTest.php** - Tests for the markdown file generation command

### Feature Tests (`tests/Feature/`)
- **DocsPageLivewireTest.php** - Integration tests for Livewire functionality
- **ConfigurationTest.php** - Tests for package configuration loading and validation
- **MarkdownParsingTest.php** - Tests for CommonMark markdown parsing with various extensions
- **SearchFunctionalityTest.php** - Tests for search features including highlighting and filtering
- **IntegrationTest.php** - End-to-end integration tests
- **ArchitectureTest.php** - Architecture and code quality tests using Pest's architecture testing
- **PerformanceTest.php** - Performance benchmarks and optimization tests

### Test Fixtures (`tests/Fixtures/`)
- **TestDocsPage.php** - Concrete implementation of DocsPage for testing
- **docs/** - Sample markdown files for testing various scenarios

## Running Tests

### Prerequisites
- PHP 8.2 or higher
- Composer dependencies installed

### Basic Test Execution
```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test file
vendor/bin/pest tests/Unit/DocsPageTest.php

# Run specific test
vendor/bin/pest --filter "can search for content"
```

### Test Categories

#### Unit Tests
```bash
vendor/bin/pest tests/Unit/
```

#### Feature Tests
```bash
vendor/bin/pest tests/Feature/
```

#### Performance Tests
```bash
vendor/bin/pest tests/Feature/PerformanceTest.php
```

#### Architecture Tests
```bash
vendor/bin/pest tests/Feature/ArchitectureTest.php
```

## Test Coverage

The test suite aims for comprehensive coverage including:

- ✅ **Core Functionality** - DocsPage methods, section loading, content parsing
- ✅ **Commands** - Page and markdown generation commands
- ✅ **Service Provider** - Package registration and asset loading
- ✅ **Plugin** - Filament plugin integration
- ✅ **Configuration** - All configuration options and defaults
- ✅ **Markdown Parsing** - CommonMark extensions and rendering
- ✅ **Search** - Full-text search, highlighting, and filtering
- ✅ **Livewire Integration** - Component lifecycle and interactions
- ✅ **Performance** - Load testing and memory usage
- ✅ **Architecture** - Code quality and structural integrity

## Key Test Features

### 1. Comprehensive Markdown Testing
Tests all supported CommonMark extensions:
- Tables
- Strikethrough
- Task lists
- Autolinks
- Footnotes
- Smart punctuation
- And more...

### 2. Search Functionality Testing
- Full-text search across content
- Search term highlighting
- Section filtering
- Performance benchmarks

### 3. Command Testing
- File generation
- Directory creation
- Template processing
- Error handling

### 4. Performance Benchmarks
- Large file processing
- Memory usage monitoring
- Concurrent access simulation
- Response time measurements

### 5. Architecture Validation
- PSR-4 compliance
- Dependency management
- Code quality standards
- Laravel conventions

## Test Data

The test suite uses realistic sample data:
- Multiple markdown files with various content types
- Complex markdown with all supported extensions
- Large content for performance testing
- Edge cases and error conditions

## Continuous Integration

The test suite runs automatically on:
- Multiple PHP versions (8.2, 8.3)
- Multiple Laravel versions (11.x)
- Multiple operating systems (Ubuntu, Windows)
- Different dependency versions (prefer-lowest, prefer-stable)

## Contributing

When adding new features:

1. **Add Unit Tests** - For individual component functionality
2. **Add Feature Tests** - For integration scenarios
3. **Update Architecture Tests** - If adding new classes or dependencies
4. **Add Performance Tests** - For features that process large amounts of data
5. **Maintain Coverage** - Ensure test coverage remains above 80%

## Test Utilities

### Custom Expectations
The test suite includes custom expectations for:
- Markdown content validation
- Search result structure verification
- Configuration validation
- Performance benchmarks

### Mocking and Fixtures
- Realistic test data in `tests/Fixtures/`
- Temporary file handling for command tests
- Memory and performance monitoring utilities

## Debugging Tests

### Enable Debug Mode
```bash
vendor/bin/pest --debug
```

### Filter by Test Name
```bash
vendor/bin/pest --filter "search"
```

### Stop on First Failure
```bash
vendor/bin/pest --stop-on-failure
```

### Coverage Reports
```bash
vendor/bin/pest --coverage-html coverage/
```

This will generate detailed HTML coverage reports in the `coverage/` directory.
