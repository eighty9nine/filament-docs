<?php

namespace EightyNine\FilamentDocs\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;

abstract class DocsPage extends Page
{
    protected static string $view = 'filament-docs::docs-page';

    public $selectedSection = '';
    public $isLoading = false;
    public $searchQuery = '';

    protected $queryString = ['selectedSection', 'searchQuery'];

    public function mount()
    {
        $sections = $this->getManualSections();
        $this->selectedSection = request()->get('selectedSection', $sections[0]['id'] ?? '');
        $this->searchQuery = request()->get('searchQuery', '');
    }

    public function selectSection($sectionId)
    {
        $this->isLoading = true;
        $this->selectedSection = $sectionId;
        $this->searchQuery = ''; // Clear search when selecting a section
        
        // Simulate loading delay for better UX
        $this->dispatch('section-loading');
        
        // Reset loading state after a brief moment
        $this->js('setTimeout(() => { $wire.set("isLoading", false); }, 300);');
    }

    public function updatedSearchQuery()
    {
        // Clear selected section when searching
        if (!empty($this->searchQuery)) {
            $this->selectedSection = '';
        } else if (empty($this->searchQuery) && empty($this->selectedSection)) {
            $sections = $this->getManualSections();
            $this->selectedSection = $sections[0]['id'] ?? '';
        }
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $sections = $this->getManualSections();
        $this->selectedSection = $sections[0]['id'] ?? '';
    }

    /**
     * Print the current section
     */
    public function printCurrentSection()
    {
        $currentSection = $this->getCurrentSection();
        if (!$currentSection) {
            return;
        }

        // Use simple window.print() with CSS media queries
        $this->js('window.print();');
    }

    /**
     * Download full documentation as PDF
     */
    public function downloadFullDocumentation()
    {
        $sections = $this->getManualSections();
        
        if (empty($sections)) {
            $this->js("alert('No documentation available to download.');");
            return;
        }

        // Simple approach - just open print dialog for full documentation
        $this->js('window.print();');
    }

    /**
     * Get the currently selected section
     */
    public function getCurrentSection(): ?array
    {
        $sections = $this->getManualSections();
        
        foreach ($sections as $section) {
            if ($section['id'] === $this->selectedSection) {
                return $section;
            }
        }
        
        return $sections[0] ?? null;
    }

    /**
     * Get filtered sections based on search query
     */
    public function getFilteredSections(): array
    {
        $sections = $this->getManualSections();
        
        if (empty($this->searchQuery)) {
            return $sections;
        }
        
        $query = strtolower($this->searchQuery);
        
        return array_filter($sections, function($section) use ($query) {
            return str_contains(strtolower($section['title']), $query) ||
                   str_contains(strtolower($section['content']), $query);
        });
    }

    /**
     * Get search results with highlighted content
     */
    public function getSearchResults(): array
    {
        if (empty($this->searchQuery)) {
            return [];
        }
        
        $sections = $this->getManualSections();
        $results = [];
        $query = strtolower($this->searchQuery);
        
        foreach ($sections as $section) {
            $matches = [];
            $content = $section['content'];
            $lines = explode("\n", $content);
            
            foreach ($lines as $lineNumber => $line) {
                if (str_contains(strtolower($line), $query)) {
                    $matches[] = [
                        'line' => $lineNumber + 1,
                        'content' => trim($line),
                        'highlighted' => $this->highlightSearchTerm($line, $this->searchQuery)
                    ];
                }
            }
            
            if (!empty($matches) || str_contains(strtolower($section['title']), $query)) {
                $results[] = [
                    'section' => $section,
                    'matches' => array_slice($matches, 0, 3), // Limit to 3 matches per section
                    'total_matches' => count($matches)
                ];
            }
        }
        
        return $results;
    }

    /**
     * Highlight search terms in text
     */
    public function highlightSearchTerm(string $text, string $term): string
    {
        return preg_replace(
            '/(' . preg_quote($term, '/') . ')/i',
            '<mark class="bg-yellow-200 text-yellow-800 px-1 rounded">$1</mark>',
            $text
        );
    }

    /**
     * Get all manual sections from markdown files
     */
    public function getManualSections(): array
    {
        $manualPath = $this->getDocsPath();
        $sections = [];
        
        if (!File::isDirectory($manualPath)) {
            return $sections;
        }
        
        $files = File::files($manualPath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilenameWithoutExtension();
                $content = File::get($file->getPathname());
                
                // Extract title from first line if it's a heading
                $lines = explode("\n", $content);
                $title = isset($lines[0]) && str_starts_with($lines[0], '#') 
                    ? trim(str_replace('#', '', $lines[0]))
                    : ucwords(str_replace(['-', '_'], ' ', $filename));
                    
                $sections[] = [
                    'id' => $filename,
                    'title' => $title,
                    'content' => $content,
                    'html' => $this->parseMarkdown($content),
                    'order' => $this->getSectionOrder($filename)
                ];
            }
        }
        
        // Sort by order
        usort($sections, fn($a, $b) => $a['order'] <=> $b['order']);
        
        return $sections;
    }

    /**
     * Parse markdown content to HTML
     */
    private function parseMarkdown(string $markdown): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        
        return $converter->convert($markdown)->getContent();
    }

    /**
     * Get section order based on filename
     * Override this method to customize section ordering
     */
    protected function getSectionOrder(string $filename): int
    {
        $orderMap = [
            'getting-started' => 1,
            'installation' => 2,
            'configuration' => 3,
            'usage' => 4,
            'api' => 5,
            'troubleshooting' => 6,
        ];
        
        return $orderMap[$filename] ?? 99;
    }

    /**
     * Get the path to markdown files
     * Override this method to specify custom docs path
     */
    abstract protected function getDocsPath(): string;
}
