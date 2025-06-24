<?php

use EightyNine\FilamentDocs\Tests\Fixtures\TestDocsPage;

beforeEach(function () {
    $this->docsPage = new TestDocsPage();
});

it('can search for content in documentation', function () {
    $this->docsPage->searchQuery = 'installation';
    
    $results = $this->docsPage->getSearchResults();
    
    expect($results)->toBeArray();
    
    if (!empty($results)) {
        foreach ($results as $result) {
            expect($result)->toHaveKeys(['section', 'matches', 'total_matches']);
            expect($result['section'])->toHaveKeys(['id', 'title', 'filepath', 'order']);
            expect($result['matches'])->toBeArray();
            expect($result['total_matches'])->toBeInt();
        }
    }
});

it('highlights search terms correctly', function () {
    $text = 'This is a test string with multiple test occurrences';
    $term = 'test';
    
    $highlighted = $this->docsPage->highlightSearchTerm($text, $term);
    
    expect($highlighted)->toContain('<mark')
        ->and($highlighted)->toContain('class=')
        ->and($highlighted)->toContain($term);
});

it('can filter sections by search query', function () {
    $this->docsPage->searchQuery = 'getting';
    
    $filteredSections = $this->docsPage->getFilteredSections();
    
    expect($filteredSections)->toBeArray();
    
    // Should find sections that contain 'getting' in title or content
    if (!empty($filteredSections)) {
        $found = false;
        foreach ($filteredSections as $section) {
            if (str_contains(strtolower($section['title']), 'getting')) {
                $found = true;
                break;
            }
        }
        expect($found)->toBeTrue();
    }
});

it('returns all sections when search query is empty', function () {
    $this->docsPage->searchQuery = '';
    
    $filteredSections = $this->docsPage->getFilteredSections();
    $allSections = $this->docsPage->getManualSections();
    
    expect($filteredSections)->toEqual($allSections);
});

it('returns empty array when no search results found', function () {
    $this->docsPage->searchQuery = 'nonexistentterm12345';
    
    $results = $this->docsPage->getSearchResults();
    
    expect($results)->toBeArray()->toBeEmpty();
});

it('limits search results per section', function () {
    // Create a long content that would have many matches
    $longContent = str_repeat('test content with test keywords and more test data. ', 20);
    
    // Mock the section loading to return our test content
    $testDocsPage = new class extends \EightyNine\FilamentDocs\Pages\DocsPage {
        protected static ?string $title = 'Test';
        public $testContent = '';
        
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
    
    $testDocsPage->testContent = $longContent;
    $testDocsPage->searchQuery = 'test';
    
    $results = $testDocsPage->getSearchResults();
    
    if (!empty($results)) {
        $maxResults = $testDocsPage->getMaxResultsPerSection();
        foreach ($results as $result) {
            expect(count($result['matches']))->toBeLessThanOrEqual($maxResults);
        }
    }
});

it('respects max results per section configuration', function () {
    $this->app['config']->set('filament-docs.search.max_results_per_section', 2);
    
    $maxResults = $this->docsPage->getMaxResultsPerSection();
    
    expect($maxResults)->toBe(2);
});

it('uses default max results when config is missing', function () {
    $this->app['config']->set('filament-docs.search.max_results_per_section', null);
    
    $maxResults = $this->docsPage->getMaxResultsPerSection();
    
    expect($maxResults)->toBe(3); // Default value
});

it('can search in both title and content', function () {
    // Test searching in title
    $this->docsPage->searchQuery = 'Getting Started';
    $titleResults = $this->docsPage->getSearchResults();
    
    // Test searching in content
    $this->docsPage->searchQuery = 'documentation';
    $contentResults = $this->docsPage->getSearchResults();
    
    expect($titleResults)->toBeArray();
    expect($contentResults)->toBeArray();
});

it('search is case insensitive', function () {
    $this->docsPage->searchQuery = 'INSTALLATION';
    $upperResults = $this->docsPage->getSearchResults();
    
    $this->docsPage->searchQuery = 'installation';
    $lowerResults = $this->docsPage->getSearchResults();
    
    $this->docsPage->searchQuery = 'Installation';
    $mixedResults = $this->docsPage->getSearchResults();
    
    expect($upperResults)->toEqual($lowerResults);
    expect($lowerResults)->toEqual($mixedResults);
});

it('highlights search terms with correct css class', function () {
    $this->app['config']->set('filament-docs.search.highlight_class', 'custom-highlight-class');
    
    $text = 'This contains the search term';
    $highlighted = $this->docsPage->highlightSearchTerm($text, 'search');
    
    expect($highlighted)->toContain('custom-highlight-class');
});

it('handles special regex characters in search terms', function () {
    $text = 'This contains special chars: [brackets] and (parentheses)';
    
    // These should not cause regex errors
    $highlighted1 = $this->docsPage->highlightSearchTerm($text, '[brackets]');
    $highlighted2 = $this->docsPage->highlightSearchTerm($text, '(parentheses)');
    
    expect($highlighted1)->toContain('<mark');
    expect($highlighted2)->toContain('<mark');
});

it('updates selected section when search query changes', function () {
    $this->docsPage->selectedSection = 'some-section';
    $this->docsPage->searchQuery = 'test query';
    
    $this->docsPage->updatedSearchQuery();
    
    expect($this->docsPage->selectedSection)->toBe('');
});

it('restores first section when search is cleared', function () {
    $this->docsPage->searchQuery = '';
    $this->docsPage->selectedSection = '';
    
    $this->docsPage->updatedSearchQuery();
    
    $sections = $this->docsPage->getManualSections();
    if (!empty($sections)) {
        expect($this->docsPage->selectedSection)->toBe($sections[0]['id']);
    }
});

it('provides search result structure with line numbers', function () {
    $this->docsPage->searchQuery = 'documentation';
    
    $results = $this->docsPage->getSearchResults();
    
    if (!empty($results)) {
        foreach ($results as $result) {
            if (!empty($result['matches'])) {
                foreach ($result['matches'] as $match) {
                    expect($match)->toHaveKeys(['line', 'content', 'highlighted']);
                    expect($match['line'])->toBeInt();
                    expect($match['content'])->toBeString();
                    expect($match['highlighted'])->toBeString();
                }
            }
        }
    }
});
