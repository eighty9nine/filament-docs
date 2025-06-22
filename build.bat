@echo off
REM Filament Docs Plugin Build Script for Windows

echo 🔧 Building Filament Docs Plugin Assets...

REM Check if npm is installed
where npm >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ npm is not installed. Please install Node.js and npm first.
    exit /b 1
)

REM Install dependencies if node_modules doesn't exist
if not exist "node_modules" (
    echo 📦 Installing dependencies...
    npm install
)

REM Build assets
echo 🏗️ Building assets...
npm run build

REM Check if build was successful
if %errorlevel% equ 0 (
    echo ✅ Build completed successfully!
    echo 📂 Assets available in resources/dist/
    dir resources\dist\
) else (
    echo ❌ Build failed!
    exit /b 1
)

echo 🎉 Filament Docs Plugin build complete!
