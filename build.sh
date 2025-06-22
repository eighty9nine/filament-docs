#!/bin/bash

# Filament Docs Plugin Build Script

echo "🔧 Building Filament Docs Plugin Assets..."

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install Node.js and npm first."
    exit 1
fi

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Build assets
echo "🏗️  Building assets..."
npm run build

# Check if build was successful
if [ $? -eq 0 ]; then
    echo "✅ Build completed successfully!"
    echo "📂 Assets available in resources/dist/"
    ls -la resources/dist/
else
    echo "❌ Build failed!"
    exit 1
fi

echo "🎉 Filament Docs Plugin build complete!"
