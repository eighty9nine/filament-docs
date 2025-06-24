<?php

use EightyNine\FilamentDocs\Tests\Fixtures\TestDocsPage;
use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->docsPage = new TestDocsPage();
    $this->filesystem = new Filesystem();
});

it('can handle large markdown files efficiently', function () {
    // Create a large markdown content
    $largeContent = '';
    for ($i = 0; $i < 1000; $i++) {
        $largeContent .= "# Section {$i}\n\n";
        $largeContent .= "This is section {$i} with **bold** text and [links](https://example.com).\n\n";
        $largeContent .= "```php\n<?php\necho 'Section {$i}';\n```\n\n";
        $largeContent .= "- Item 1 for section {$i}\n";
        $largeContent .= "- Item 2 for section {$i}\n\n";
    }
    
    $start = microtime(true);
    
    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $largeContent);
    
    $end = microtime(true);
    $duration = $end - $start;
    
    expect($html)->toBeString();
    expect($duration)->toBeLessThan(5.0); // Should parse in less than 5 seconds
});

it('can handle many documentation files efficiently', function () {
    $testPath = storage_path('framework/testing/performance');
    
    // Clean up
    if ($this->filesystem->exists($testPath)) {
        $this->filesystem->deleteDirectory($testPath);
    }
    
    $this->filesystem->makeDirectory($testPath, 0755, true);
    
    // Create many markdown files
    for ($i = 1; $i <= 50; $i++) {
        $content = "# Document {$i}\n\nThis is document number {$i}.\n\n## Content\n\nSome content here.";
        file_put_contents($testPath . "/document-{$i}.md", $content);
    }
    
    // Create a docs page pointing to this directory
    $docsPage = new class($testPath) extends \EightyNine\FilamentDocs\Pages\DocsPage {
        private string $path;
        
        public function __construct(string $path)
        {
            $this->path = $path;
        }
        
        protected static ?string $title = 'Performance Test';
        
        protected function getDocsPath(): string
        {
            return $this->path;
        }
    };
    
    $start = microtime(true);
    
    $sections = $docsPage->getManualSections();
    
    $end = microtime(true);
    $duration = $end - $start;
    
    expect($sections)->toHaveCount(50);
    expect($duration)->toBeLessThan(2.0); // Should load in less than 2 seconds
    
    // Clean up
    $this->filesystem->deleteDirectory($testPath);
});

it('can search through large content efficiently', function () {
    // Create content with many search matches
    $content = str_repeat('This is a test document with many test keywords and test phrases. ', 1000);
    
    $docsPage = new class extends \EightyNine\FilamentDocs\Pages\DocsPage {
        public $testContent = '';
        
        protected static ?string $title = 'Search Performance Test';
        
        protected function getDocsPath(): string
        {
            return __DIR__ . '/../Fixtures/docs';
        }
        
        public function loadSectionContent(string $sectionId): ?array
        {
            return [
                'id' => $sectionId,
                'title' => 'Test Section',
                'content' => $this->testContent,
                'html' => '<p>' . $this->testContent . '</p>',
                'order' => 1,
                'filepath' => 'test.md'
            ];
        }
    };
    
    $docsPage->testContent = $content;
    $docsPage->searchQuery = 'test';
    
    $start = microtime(true);
    
    $results = $docsPage->getSearchResults();
    
    $end = microtime(true);
    $duration = $end - $start;
    
    expect($results)->toBeArray();
    expect($duration)->toBeLessThan(1.0); // Should search in less than 1 second
});

it('caches sections for performance', function () {
    // First call should populate cache
    $start1 = microtime(true);
    $sections1 = $this->docsPage->getManualSections();
    $end1 = microtime(true);
    $duration1 = $end1 - $start1;
    
    // Second call should use cache and be faster
    $start2 = microtime(true);
    $sections2 = $this->docsPage->getManualSections();
    $end2 = microtime(true);
    $duration2 = $end2 - $start2;
    
    expect($sections1)->toEqual($sections2);
    expect($duration2)->toBeLessThan($duration1); // Second call should be faster
});

it('can handle concurrent access efficiently', function () {
    // Simulate multiple concurrent requests
    $results = [];
    
    for ($i = 0; $i < 10; $i++) {
        $start = microtime(true);
        
        $sections = $this->docsPage->getManualSections();
        $currentSection = $this->docsPage->getCurrentSection();
        
        $end = microtime(true);
        $duration = $end - $start;
        
        $results[] = $duration;
    }
    
    $averageDuration = array_sum($results) / count($results);
    
    expect($averageDuration)->toBeLessThan(0.1); // Average should be less than 100ms
});

it('memory usage stays reasonable with large files', function () {
    $memoryBefore = memory_get_usage();
    
    // Process multiple large sections
    for ($i = 0; $i < 10; $i++) {
        $largeContent = str_repeat("# Large Section {$i}\n\nContent here.\n\n", 1000);
        
        $reflection = new ReflectionClass($this->docsPage);
        $method = $reflection->getMethod('parseMarkdown');
        $method->setAccessible(true);
        
        $html = $method->invoke($this->docsPage, $largeContent);
    }
    
    $memoryAfter = memory_get_usage();
    $memoryUsed = $memoryAfter - $memoryBefore;
    
    // Should not use more than 50MB for processing
    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024);
});

it('can handle search highlighting efficiently', function () {
    $text = str_repeat('This contains the search term multiple times. ', 1000);
    
    $start = microtime(true);
    
    $highlighted = $this->docsPage->highlightSearchTerm($text, 'search');
    
    $end = microtime(true);
    $duration = $end - $start;
    
    expect($highlighted)->toContain('<mark');
    expect($duration)->toBeLessThan(0.1); // Should highlight in less than 100ms
});

it('processes markdown extensions efficiently', function () {
    $complexMarkdown = '
# Title

## Table

| Column 1 | Column 2 | Column 3 |
|----------|----------|----------|
| Data 1   | Data 2   | Data 3   |

## Code

```php
<?php
class Example {
    public function test() {
        return "Hello World!";
    }
}
```

## Lists

- [x] Task 1
- [ ] Task 2
- [x] Task 3

## Links

Visit https://example.com for more info.

## Formatting

This is **bold** and *italic* and ~~strikethrough~~.

> Quote here

### Footnotes

This has a footnote[^1].

[^1]: Footnote content.
';
    
    $start = microtime(true);
    
    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $complexMarkdown);
    
    $end = microtime(true);
    $duration = $end - $start;
    
    expect($html)->toBeString();
    expect($duration)->toBeLessThan(0.5); // Should parse complex markdown in less than 500ms
});
