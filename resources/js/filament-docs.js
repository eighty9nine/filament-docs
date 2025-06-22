/**
 * Filament Docs Plugin JavaScript
 * 
 * Enhanced functionality for the documentation system
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize documentation features
    initializeSearchHighlighting();
    initializeKeyboardNavigation();
    initializePrintFeatures();
    initializeScrollToTop();
    initializeCodeCopyButtons();
});

/**
 * Enhanced search result highlighting
 */
function initializeSearchHighlighting() {
    const searchInput = document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms="searchQuery"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            if (query.length > 2) {
                highlightSearchTerms(query);
            } else {
                clearSearchHighlights();
            }
        });
    }
}

/**
 * Highlight search terms in content
 */
function highlightSearchTerms(query) {
    const content = document.querySelector('.filament-docs-prose');
    if (!content) return;

    // Remove existing highlights
    clearSearchHighlights();

    // Create regex for search term
    const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
    
    // Find and highlight text nodes
    walkTextNodes(content, function(node) {
        if (regex.test(node.textContent)) {
            const parent = node.parentNode;
            const wrapper = document.createElement('span');
            wrapper.innerHTML = node.textContent.replace(regex, '<mark class="filament-docs-search-highlight">$1</mark>');
            
            // Replace text node with highlighted content
            while (wrapper.firstChild) {
                parent.insertBefore(wrapper.firstChild, node);
            }
            parent.removeChild(node);
        }
    });
}

/**
 * Clear search highlights
 */
function clearSearchHighlights() {
    const highlights = document.querySelectorAll('.filament-docs-search-highlight');
    highlights.forEach(highlight => {
        const parent = highlight.parentNode;
        parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
        parent.normalize();
    });
}

/**
 * Walk through text nodes in an element
 */
function walkTextNodes(element, callback) {
    const walker = document.createTreeWalker(
        element,
        NodeFilter.SHOW_TEXT,
        null,
        false
    );

    const textNodes = [];
    let node;

    while (node = walker.nextNode()) {
        textNodes.push(node);
    }

    textNodes.forEach(callback);
}

/**
 * Escape special regex characters
 */
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\\\$&');
}

/**
 * Keyboard navigation for sections
 */
function initializeKeyboardNavigation() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms="searchQuery"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }

        // Arrow keys for navigation (when not in input)
        if (!e.target.matches('input, textarea')) {
            if (e.key === 'ArrowLeft') {
                const prevButton = document.querySelector('[wire\\:click*="selectSection"]:has([class*="arrow-left"])');
                if (prevButton) {
                    prevButton.click();
                }
            } else if (e.key === 'ArrowRight') {
                const nextButton = document.querySelector('[wire\\:click*="selectSection"]:has([class*="arrow-right"])');
                if (nextButton) {
                    nextButton.click();
                }
            }
        }

        // Escape to clear search
        if (e.key === 'Escape') {
            const clearButton = document.querySelector('[wire\\:click="clearSearch"]');
            if (clearButton) {
                clearButton.click();
            }
        }
    });
}

/**
 * Print functionality enhancements
 */
function initializePrintFeatures() {
    // Add print styles dynamically
    const printStyles = document.createElement('style');
    printStyles.textContent = `
        @media print {
            .no-print { display: none !important; }
            .print-break-before { page-break-before: always; }
            .print-break-after { page-break-after: always; }
            .print-break-inside-avoid { page-break-inside: avoid; }
            
            /* Ensure content is visible when printing */
            .filament-docs-prose * {
                color: #000 !important;
                background: transparent !important;
            }
            
            .filament-docs-prose h1,
            .filament-docs-prose h2,
            .filament-docs-prose h3 {
                page-break-after: avoid;
            }
            
            .filament-docs-prose pre,
            .filament-docs-prose blockquote,
            .filament-docs-table {
                page-break-inside: avoid;
            }
        }
    `;
    document.head.appendChild(printStyles);

    // Add print section markers
    window.addEventListener('beforeprint', function() {
        document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', function() {
        document.body.classList.remove('printing');
    });
}

/**
 * Scroll to top functionality
 */
function initializeScrollToTop() {
    // Create scroll to top button
    const scrollButton = document.createElement('button');
    scrollButton.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    `;
    scrollButton.className = 'fixed bottom-8 right-8 bg-primary-600 hover:bg-primary-700 text-white p-3 rounded-full shadow-lg transition-all duration-200 opacity-0 pointer-events-none z-50';
    scrollButton.setAttribute('aria-label', 'Scroll to top');
    
    document.body.appendChild(scrollButton);

    // Show/hide scroll button based on scroll position
    function toggleScrollButton() {
        if (window.scrollY > 500) {
            scrollButton.classList.remove('opacity-0', 'pointer-events-none');
            scrollButton.classList.add('opacity-100');
        } else {
            scrollButton.classList.add('opacity-0', 'pointer-events-none');
            scrollButton.classList.remove('opacity-100');
        }
    }

    window.addEventListener('scroll', toggleScrollButton);
    
    scrollButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Add copy buttons to code blocks
 */
function initializeCodeCopyButtons() {
    const codeBlocks = document.querySelectorAll('.filament-docs-prose pre code');
    
    codeBlocks.forEach(function(codeBlock) {
        const pre = codeBlock.parentElement;
        
        // Create copy button
        const copyButton = document.createElement('button');
        copyButton.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
        `;
        copyButton.className = 'absolute top-2 right-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 p-2 rounded text-xs transition-colors duration-200 opacity-0 group-hover:opacity-100';
        copyButton.setAttribute('aria-label', 'Copy code');
        
        // Make pre relative for absolute positioning
        pre.style.position = 'relative';
        pre.classList.add('group');
        
        // Add copy functionality
        copyButton.addEventListener('click', function() {
            const code = codeBlock.textContent;
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(function() {
                    showCopySuccess(copyButton);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = code;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    showCopySuccess(copyButton);
                } catch (err) {
                    console.error('Failed to copy code:', err);
                }
                
                document.body.removeChild(textArea);
            }
        });
        
        pre.appendChild(copyButton);
    });
}

/**
 * Show copy success feedback
 */
function showCopySuccess(button) {
    const originalHTML = button.innerHTML;
    
    button.innerHTML = `
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    `;
    button.classList.add('text-green-600');
    
    setTimeout(function() {
        button.innerHTML = originalHTML;
        button.classList.remove('text-green-600');
    }, 2000);
}

/**
 * Smooth scrolling for anchor links
 */
document.addEventListener('click', function(e) {
    if (e.target.matches('a[href^="#"]')) {
        e.preventDefault();
        
        const targetId = e.target.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

/**
 * Enhanced focus management for accessibility
 */
function initializeFocusManagement() {
    // Add focus-visible polyfill functionality
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('user-is-tabbing');
        }
    });
    
    document.addEventListener('mousedown', function() {
        document.body.classList.remove('user-is-tabbing');
    });
}

// Initialize focus management
initializeFocusManagement();

/**
 * Auto-save search preferences
 */
function initializeSearchPreferences() {
    const searchInput = document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms="searchQuery"]');
    
    if (searchInput) {
        // Restore last search
        const lastSearch = localStorage.getItem('filament-docs-last-search');
        if (lastSearch && !searchInput.value) {
            searchInput.value = lastSearch;
            searchInput.dispatchEvent(new Event('input'));
        }
        
        // Save search on change
        searchInput.addEventListener('input', function(e) {
            if (e.target.value.trim()) {
                localStorage.setItem('filament-docs-last-search', e.target.value);
            } else {
                localStorage.removeItem('filament-docs-last-search');
            }
        });
    }
}

// Initialize search preferences
initializeSearchPreferences();
