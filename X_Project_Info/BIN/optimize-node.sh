#!/bin/bash

echo "🔍 Checking for Node.js performance issues..."

# Check Node.js version
echo "Node.js version:"
node -v

# Check for active node processes and their CPU usage
echo -e "\n📊 Current Node.js processes:"
ps -eo pid,%cpu,%mem,command | grep -E '[n]ode|[n]pm' | sort -k2 -r

# Check for large node_modules
echo -e "\n📦 Analyzing node_modules size:"
du -sh node_modules
du -sh node_modules/* | sort -hr | head -10

echo -e "\n🔧 Optimization suggestions:"
echo "1. Consider running 'npm prune' to remove unused dependencies"
echo "2. Try using 'export NODE_OPTIONS=--max-old-space-size=4096' to increase memory limit"
echo "3. For development, use 'npm run build' instead of 'npm run dev' to avoid file watchers"
echo "4. Check 'docs/performance-optimization.md' for more detailed advice"

echo -e "\n🛠 Quick fixes:"
echo "# Stop all Node.js processes:"
echo "killall node"
echo ""
echo "# Rebuild node_modules (if needed):"
echo "rm -rf node_modules package-lock.json"
echo "npm install"
echo ""
echo "# Build assets without watching:"
echo "npm run build"
