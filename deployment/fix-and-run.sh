#!/bin/bash

# Fix file paths and run setup
echo "🔧 Fixing file paths and running Farm ERP setup..."

# Make sure we're in the right directory
cd /root/deployment

# Make all scripts executable
chmod +x *.sh

# Run the corrected setup script
./complete-setup-ip.sh