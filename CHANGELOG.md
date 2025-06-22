# Changelog

All notable changes to `filament-docs` will be documented in this file.

## 1.0.0 - 2024-12-28

### Added
- Initial release
- Markdown-based documentation system
- Real-time search functionality with highlighting
- Responsive sidebar navigation
- Section progress tracking
- Loading indicators and smooth transitions
- Persistent state across browser refreshes
- Multiple markdown templates (basic, guide, api, troubleshooting, feature)
- Artisan commands for creating documentation pages and markdown files
- Full internationalization support
- Customizable styling and configuration
- Mobile-responsive design
- Print and share functionality
- CommonMark integration for markdown parsing
- Search result highlighting with line number references
- Previous/Next navigation between sections
- Configuration-driven section ordering
- Professional UI with gradient headers and modern design

### Features
- **DocsPage Base Class**: Extensible base class for creating documentation pages
- **Search System**: Powerful search with debouncing, highlighting, and contextual results
- **Navigation**: Smart sidebar navigation with visual indicators
- **Commands**: `make:filament-docs-page` and `make:filament-docs-markdown` commands
- **Templates**: Pre-built templates for different types of documentation
- **Translations**: Full support for multiple languages
- **Customization**: Extensive configuration options and customization hooks
- **Performance**: Optimized loading and efficient markdown parsing
- **Accessibility**: Screen reader friendly and keyboard navigation support

### Technical Details
- Compatible with Filament 3.x
- Requires PHP 8.2+
- Uses League CommonMark for markdown parsing
- Livewire-powered reactive components
- Tailwind CSS styling
- Spatie Package Tools integration
