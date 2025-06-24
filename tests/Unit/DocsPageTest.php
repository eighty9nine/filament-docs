<?php

use EightyNine\FilamentDocs\Tests\Fixtures\TestDocsPage;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->docsPage = new TestDocsPage();
});

it('can get manual sections', function () {
    $sections = $this->docsPage->getManualSections();
    
    expect($sections)->toBeArray()
        ->and($sections)->not->toBeEmpty();
    
    // Check first section structure
    $firstSection = $sections[0];
    expect($firstSection)->toHaveKeys(['id', 'title', 'filepath', 'order']);
});

it('can load section content', function () {
    $sections = $this->docsPage->getManualSections();
    $firstSectionId = $sections[0]['id'];
    
    $content = $this->docsPage->loadSectionContent($firstSectionId);
    
    expect($content)->toBeArray()
        ->and($content)->toHaveKeys(['id', 'title', 'content', 'html', 'order', 'filepath'])
        ->and($content['html'])->toBeString()
        ->and($content['content'])->toBeString();
});

it('can get current section', function () {
    $currentSection = $this->docsPage->getCurrentSection();
    
    expect($currentSection)->toBeArray()
        ->and($currentSection)->toHaveKeys(['id', 'title', 'content', 'html', 'order', 'filepath']);
});

it('can search sections', function () {
    $this->docsPage->searchQuery = 'test';
    
    $results = $this->docsPage->getSearchResults();
    
    expect($results)->toBeArray();
    
    if (!empty($results)) {
        $firstResult = $results[0];
        expect($firstResult)->toHaveKeys(['section', 'matches', 'total_matches']);
    }
});

it('can highlight search terms', function () {
    $text = 'This is a test string with test words';
    $highlighted = $this->docsPage->highlightSearchTerm($text, 'test');
    
    expect($highlighted)->toContain('<mark')
        ->and($highlighted)->toContain('test');
});

it('can filter sections by search query', function () {
    $this->docsPage->searchQuery = 'installation';
    
    $filteredSections = $this->docsPage->getFilteredSections();
    
    expect($filteredSections)->toBeArray();
    
    if (!empty($filteredSections)) {
        // Should find the installation section
        $foundInstallation = false;
        foreach ($filteredSections as $section) {
            if (str_contains(strtolower($section['title']), 'installation')) {
                $foundInstallation = true;
                break;
            }
        }
        expect($foundInstallation)->toBeTrue();
    }
});

it('can select a section', function () {
    $sections = $this->docsPage->getManualSections();
    $sectionId = $sections[0]['id'];
    
    $this->docsPage->selectSection($sectionId);
    
    expect($this->docsPage->selectedSection)->toBe($sectionId);
});

it('can clear search', function () {
    $this->docsPage->searchQuery = 'test';
    $this->docsPage->selectedSection = '';
    
    $this->docsPage->clearSearch();
    
    expect($this->docsPage->searchQuery)->toBe('')
        ->and($this->docsPage->selectedSection)->not->toBe('');
});

it('can get section order', function () {
    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('getSectionOrder');
    $method->setAccessible(true);
    
    $order = $method->invoke($this->docsPage, 'getting-started');
    
    expect($order)->toBeInt();
});

it('can get title', function () {
    $title = $this->docsPage->getTitle();
    
    expect($title)->toBe('Test Documentation');
});

it('can get docs path', function () {
    $path = $this->docsPage->getDocsPath();
    
    expect($path)->toBeString()
        ->and($path)->toContain('docs');
});

it('parses markdown correctly', function () {
    $markdown = '# Test Heading

This is a **bold** text with *italic* text.

```php
echo "Hello World!";
```

- List item 1
- List item 2';

    $reflection = new ReflectionClass($this->docsPage);
    $method = $reflection->getMethod('parseMarkdown');
    $method->setAccessible(true);
    
    $html = $method->invoke($this->docsPage, $markdown);
    
    expect($html)->toContain('<h1>')
        ->and($html)->toContain('<strong>')
        ->and($html)->toContain('<em>')
        ->and($html)->toContain('<code>')
        ->and($html)->toContain('<ul>');
});

it('handles non-existent section gracefully', function () {
    $content = $this->docsPage->loadSectionContent('non-existent-section');
    
    expect($content)->toBeNull();
});

it('can get all sections with content for printing', function () {
    $sectionsWithContent = $this->docsPage->getAllSectionsWithContent();
    
    expect($sectionsWithContent)->toBeArray();
    
    foreach ($sectionsWithContent as $section) {
        expect($section)->toHaveKeys(['id', 'title', 'content', 'html', 'order', 'filepath']);
    }
});

it('can reset print flags', function () {
    $this->docsPage->isPrintingCurrent = true;
    $this->docsPage->isPrintingAll = true;
    
    $this->docsPage->resetPrintFlags();
    
    expect($this->docsPage->isPrintingCurrent)->toBeFalse()
        ->and($this->docsPage->isPrintingAll)->toBeFalse();
});

it('updates search query correctly', function () {
    $this->docsPage->selectedSection = 'test-section';
    $this->docsPage->searchQuery = 'test query';
    
    $this->docsPage->updatedSearchQuery();
    
    expect($this->docsPage->selectedSection)->toBe('');
    
    // Test clearing search query
    $this->docsPage->searchQuery = '';
    $this->docsPage->updatedSearchQuery();
    
    expect($this->docsPage->selectedSection)->not->toBe('');
});
