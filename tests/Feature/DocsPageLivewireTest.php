<?php

use EightyNine\FilamentDocs\Tests\Fixtures\TestDocsPage;

beforeEach(function () {
    $this->docsPage = new TestDocsPage();
});

it('can render docs page view', function () {
    $view = view('filament-docs::docs-page', [
        'page' => $this->docsPage
    ]);
    
    expect($view)->not->toBeNull();
});

it('integrates with livewire', function () {
    // Test that the page can be mounted as a Livewire component
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    expect($component)->not->toBeNull();
    
    $component->assertSet('selectedSection', function ($value) {
        return !empty($value);
    });
});

it('can handle section selection through livewire', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $sections = $component->instance()->getManualSections();
    if (!empty($sections)) {
        $sectionId = $sections[0]['id'];
        
        $component->call('selectSection', $sectionId)
            ->assertSet('selectedSection', $sectionId)
            ->assertSet('searchQuery', '');
    }
});

it('can handle search through livewire', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->set('searchQuery', 'test')
        ->assertSet('searchQuery', 'test')
        ->assertSet('selectedSection', '');
});

it('can clear search through livewire', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->set('searchQuery', 'test')
        ->call('clearSearch')
        ->assertSet('searchQuery', '')
        ->assertSet('selectedSection', function ($value) {
            return !empty($value);
        });
});

it('can print current section through livewire', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->call('printCurrentSection')
        ->assertSet('isPrintingCurrent', true);
});

it('can download full documentation through livewire', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->call('downloadFullDocumentation')
        ->assertSet('isPrintingAll', true);
});

it('can reset print flags through livewire', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->set('isPrintingCurrent', true)
        ->set('isPrintingAll', true)
        ->call('resetPrintFlags')
        ->assertSet('isPrintingCurrent', false)
        ->assertSet('isPrintingAll', false);
});

it('maintains query string parameters', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    // Check that queryString property exists
    $queryString = $component->instance()->queryString;
    
    expect($queryString)->toContain('selectedSection', 'searchQuery');
});

it('handles missing documentation gracefully', function () {
    // Create a docs page with non-existent path
    $docsPage = new class extends \EightyNine\FilamentDocs\Pages\DocsPage {
        protected static ?string $title = 'Empty Docs';
        
        protected function getDocsPath(): string
        {
            return '/non/existent/path';
        }
    };
    
    $sections = $docsPage->getManualSections();
    expect($sections)->toBeArray()->toBeEmpty();
    
    $currentSection = $docsPage->getCurrentSection();
    expect($currentSection)->toBeNull();
});

it('can handle livewire mount with query parameters', function () {
    // Simulate request with query parameters
    request()->merge([
        'selectedSection' => 'getting-started',
        'searchQuery' => 'test'
    ]);
    
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->assertSet('selectedSection', 'getting-started')
        ->assertSet('searchQuery', 'test');
});

it('can navigate between sections', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $sections = $component->instance()->getManualSections();
    
    if (count($sections) > 1) {
        $firstSection = $sections[0]['id'];
        $secondSection = $sections[1]['id'];
        
        $component->call('selectSection', $firstSection)
            ->assertSet('selectedSection', $firstSection)
            ->call('selectSection', $secondSection)
            ->assertSet('selectedSection', $secondSection);
    }
});

it('can perform live search and get results', function () {
    $component = \Livewire\Livewire::test(TestDocsPage::class);
    
    $component->set('searchQuery', 'installation');
    
    $searchResults = $component->instance()->getSearchResults();
    expect($searchResults)->toBeArray();
    
    // If there are results, verify structure
    if (!empty($searchResults)) {
        $firstResult = $searchResults[0];
        expect($firstResult)->toHaveKeys(['section', 'matches', 'total_matches']);
    }
});
