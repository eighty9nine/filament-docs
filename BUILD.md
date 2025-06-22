# Filament Docs Plugin - Build System

This document explains how to build the assets for the Filament Docs plugin.

## Prerequisites

- Node.js (version 16 or higher)
- npm (comes with Node.js)

## Setup

1. **Install dependencies:**
   ```bash
   npm install
   ```

## Building Assets

### Development Build
For development with hot reloading:
```bash
npm run dev
```

### Production Build
For optimized production assets:
```bash
npm run build
```

### Watch Mode
To automatically rebuild when files change:
```bash
npm run watch
```

### Using Composer Scripts
You can also use the composer scripts:
```bash
composer run build-assets    # Production build
composer run build-dev      # Development build
composer run build-watch    # Watch mode
```

### Platform-specific Scripts
For convenience, platform-specific build scripts are provided:

**Windows:**
```cmd
build.bat
```

**Unix/Linux/macOS:**
```bash
./build.sh
```

## File Structure

```
resources/
├── css/
│   └── filament-docs.css     # Main stylesheet (Tailwind CSS)
├── js/
│   └── filament-docs.js      # JavaScript functionality
└── dist/                     # Built assets (generated)
    ├── filament-docs.css     # Compiled CSS
    └── filament-docs.js      # Compiled JavaScript
```

## CSS Architecture

The CSS is built using Tailwind CSS and follows this structure:

### Layers
- **Base**: CSS custom properties for Filament theme integration
- **Components**: Reusable UI components with `.filament-docs-*` prefix
- **Utilities**: Custom utility classes and responsive design

### Key Features
- **Dark/Light Mode**: Full support for Filament's theming system
- **CSS Custom Properties**: Integration with Filament's color system
- **Component Classes**: Modular, reusable component styles
- **Typography**: Enhanced prose styling for documentation content
- **Responsive Design**: Mobile-first responsive components

### Component Classes
All component classes use the `filament-docs-` prefix:

- `.filament-docs-search-highlight` - Search result highlighting
- `.filament-docs-section-button` - Navigation section buttons
- `.filament-docs-content-card` - Main content containers
- `.filament-docs-table` - Enhanced table styling
- `.filament-docs-prose` - Typography for documentation content

## JavaScript Features

The JavaScript file provides enhanced functionality:

- **Search Enhancement**: Real-time search highlighting
- **Keyboard Navigation**: Arrow keys and keyboard shortcuts
- **Copy to Clipboard**: Copy code blocks with one click
- **Scroll to Top**: Smooth scrolling and scroll-to-top button
- **Print Support**: Enhanced printing with proper page breaks
- **Accessibility**: Focus management and keyboard navigation

## Theme Integration

The plugin integrates seamlessly with Filament's theming system:

1. **CSS Custom Properties**: Uses Filament's color variables
2. **Dark Mode**: Automatic dark/light mode switching
3. **Typography**: Follows Filament's typography scale
4. **Components**: Consistent with Filament's design language

## Development Workflow

1. **Make Changes**: Edit files in `resources/css/` or `resources/js/`
2. **Build Assets**: Run `npm run build` or `npm run watch`
3. **Test**: The built assets in `resources/dist/` are automatically loaded
4. **Commit**: Include both source and built files in version control

## Troubleshooting

### Build Fails
- Ensure Node.js and npm are installed
- Clear node_modules and reinstall: `rm -rf node_modules && npm install`
- Check for syntax errors in source files

### Assets Not Loading
- Verify assets are built in `resources/dist/`
- Check file permissions
- Clear application cache

### Styling Issues
- Ensure Tailwind CSS is processing correctly
- Check for CSS conflicts with existing styles
- Verify dark mode classes are applied correctly

## Configuration

The build system can be customized by editing:

- `tailwind.config.js` - Tailwind CSS configuration
- `vite.config.js` - Vite build configuration
- `postcss.config.js` - PostCSS plugins
- `package.json` - Build scripts and dependencies

## Production Deployment

For production deployment:

1. Run `npm run build` to create optimized assets
2. The `resources/dist/` folder contains the final assets
3. These assets are automatically registered by the service provider
4. No additional configuration needed in the Laravel application
