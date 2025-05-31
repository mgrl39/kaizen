#!/bin/bash

# Exit on error
set -e

echo "🚀 Initializing backend..."

# Install composer dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing composer dependencies..."
    composer install
fi

# Publish Telescope assets
echo "🔭 Publishing Telescope assets..."
php artisan telescope:publish

echo "✅ Backend initialization completed!" 