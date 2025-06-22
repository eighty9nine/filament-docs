<?php

namespace EightyNine\FilamentDocs\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;

abstract class DocsPage extends Page
{
    protected static string $view = 'filament-docs::docs-page';
    
    public $selectedSection = '';
    public $isLoading = false;
    public $searchQuery = '';
    public $isPrintingCurrent = false;
    public $isPrintingAll = false;
    
    protected $queryString = ['selectedSection', 'searchQuery'];
    
    private $sectionsCache = null;

    public function mount()
    {
        $sections = $this->getManualSections();
        $this->selectedSection = request()->get('selectedSection', $sections[0]['id'] ?? '');
        $this->searchQuery = request()->get('searchQuery', '');
    }    public function selectSection($sectionId)
    {
        $this->isLoading = true;
        $this->selectedSection = $sectionId;
        $this->searchQuery = ''; // Clear search when selecting a section
        
        // Force refresh of the component
        $this->dispatch('$refresh');
        
        // Reset loading state after a brief moment
        $this->js('setTimeout(() => { $wire.set("isLoading", false); }, 100);');
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
    }    /**
     * Print the current section
     */
    public function printCurrentSection()
    {
        $currentSection = $this->getCurrentSection();
        if (!$currentSection) {
            return;
        }

        // Set the print flag to trigger Alpine.js print
        $this->isPrintingCurrent = true;
    }    /**
     * Download full documentation as PDF
     */
    public function downloadFullDocumentation()
    {
        $sections = $this->getManualSections();
        
        if (empty($sections)) {
            $this->addError('download', 'No documentation available to download.');
            return;
        }

        // Set the print flag to trigger Alpine.js print
        $this->isPrintingAll = true;
    }

    /**
     * Get all sections with content for printing/downloading
     */
    public function getAllSectionsWithContent(): array
    {
        $sections = $this->getManualSections();
        $sectionsWithContent = [];
        
        foreach ($sections as $section) {
            $sectionWithContent = $this->loadSectionContent($section['id']);
            if ($sectionWithContent) {
                $sectionsWithContent[] = $sectionWithContent;
            }
        }
        
        return $sectionsWithContent;
    }

    /**
     * Reset print flags after printing
     */
    public function resetPrintFlags()
    {
        $this->isPrintingCurrent = false;
        $this->isPrintingAll = false;
    }    /**
     * Get the currently selected section with content
     */
    public function getCurrentSection(): ?array
    {
        if (empty($this->selectedSection)) {
            $sections = $this->getManualSections();
            if (empty($sections)) {
                return null;
            }
            $this->selectedSection = $sections[0]['id'];
        }
        
        return $this->loadSectionContent($this->selectedSection);
    }

    /**
     * Computed property for current section data
     */
    public function getCurrentSectionProperty(): ?array
    {
        return $this->getCurrentSection();
    }

    /**
     * Get current section data for the view
     */
    public function getCurrentSectionData(): array
    {
        $currentSection = $this->getCurrentSection();
        
        if (!$currentSection) {
            return [
                'title' => 'No Documentation Found',
                'html' => '<p>No documentation files found.</p>',
                'id' => ''
            ];
        }
        
        return $currentSection;
    }/**
     * Get filtered sections based on search query
     */
    public function getFilteredSections(): array
    {
        if (empty($this->searchQuery)) {
            return $this->getManualSections();
        }
        
        $sections = $this->getManualSections();
        $filteredSections = [];
        $query = strtolower($this->searchQuery);
        
        foreach ($sections as $section) {
            // First check title
            if (str_contains(strtolower($section['title']), $query)) {
                $filteredSections[] = $section;
                continue;
            }
            
            // If title doesn't match, load content and check
            $sectionWithContent = $this->loadSectionContent($section['id']);
            if ($sectionWithContent && str_contains(strtolower($sectionWithContent['content']), $query)) {
                $filteredSections[] = $section;
            }
        }
        
        return $filteredSections;
    }    /**
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
            $hasMatches = false;
            
            // Check title first
            if (str_contains(strtolower($section['title']), $query)) {
                $hasMatches = true;
            }
            
            // Load content only if needed for search
            $sectionWithContent = $this->loadSectionContent($section['id']);
            if (!$sectionWithContent) {
                continue;
            }
            
            $content = $sectionWithContent['content'];
            $lines = explode("\n", $content);
            
            foreach ($lines as $lineNumber => $line) {
                if (str_contains(strtolower($line), $query)) {
                    $hasMatches = true;
                    $matches[] = [
                        'line' => $lineNumber + 1,
                        'content' => trim($line),
                        'highlighted' => $this->highlightSearchTerm($line, $this->searchQuery)
                    ];
                }
            }
            
            if ($hasMatches) {
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
    }    /**
     * Get all manual sections metadata (without content)
     */
    public function getManualSections(): array
    {
        if ($this->sectionsCache !== null) {
            return $this->sectionsCache;
        }
        
        $manualPath = $this->getDocsPath();
        $sections = [];
        
        if (!File::isDirectory($manualPath)) {
            return $sections;
        }
        
        $files = File::files($manualPath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilenameWithoutExtension();
                
                // Extract title from first line if it's a heading (read only first few lines)
                $fileHandle = fopen($file->getPathname(), 'r');
                $firstLine = fgets($fileHandle);
                fclose($fileHandle);
                
                $title = $firstLine && str_starts_with($firstLine, '#') 
                    ? trim(str_replace('#', '', $firstLine))
                    : ucwords(str_replace(['-', '_'], ' ', $filename));
                    
                $sections[] = [
                    'id' => $filename,
                    'title' => $title,
                    'filepath' => $file->getPathname(),
                    'order' => $this->getSectionOrder($filename)
                ];
            }
        }
        
        // Sort by order
        usort($sections, fn($a, $b) => $a['order'] <=> $b['order']);
        
        $this->sectionsCache = $sections;
        return $sections;
    }    /**
     * Load content for a specific section
     */
    public function loadSectionContent(string $sectionId): ?array
    {
        $sections = $this->getManualSections();
        
        foreach ($sections as $section) {
            if ($section['id'] === $sectionId) {
                if (!File::exists($section['filepath'])) {
                    return null;
                }
                
                $content = File::get($section['filepath']);
                
                return [
                    'id' => $section['id'],
                    'title' => $section['title'],
                    'content' => $content,
                    'html' => $this->parseMarkdown($content),
                    'order' => $section['order'],
                    'filepath' => $section['filepath']
                ];
            }
        }
        
        return null;
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
    }    /**
     * Get the title for the documentation
     * Override this method to customize the title
     */
    public function getTitle(): string
    {
        return static::$title ?? 'Documentation';
    }

    /**
     * Get the path to markdown files
     * Override this method to specify custom docs path
     */
    abstract protected function getDocsPath(): string;
}
