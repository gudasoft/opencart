#!/bin/bash

# Build script for dual-prices OCMOD extension
# This script creates a new zip file from the dual-prices.ocmod directory

set -e  # Exit on error

EXTENSION_DIR="dual-prices.ocmod"
ZIP_FILE="dual-prices.ocmod.zip"

# Check if the extension directory exists
if [ ! -d "$EXTENSION_DIR" ]; then
    echo "Error: Extension directory '$EXTENSION_DIR' not found!"
    exit 1
fi

# Remove existing zip file if it exists
if [ -f "$ZIP_FILE" ]; then
    echo "Removing existing $ZIP_FILE..."
    rm "$ZIP_FILE"
fi

# Create new zip file
echo "Creating new $ZIP_FILE..."
cd "$EXTENSION_DIR"
zip -r "../$ZIP_FILE" .

# Move back to original directory
cd ..

# Verify the zip file was created
if [ -f "$ZIP_FILE" ]; then
    echo "✓ Successfully created $ZIP_FILE"
    echo "Contents:"
    unzip -l "$ZIP_FILE"
    echo "File size: $(ls -lh "$ZIP_FILE" | awk '{print $5}')"
else
    echo "✗ Failed to create $ZIP_FILE"
    exit 1
fi