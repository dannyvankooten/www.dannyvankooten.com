#!/usr/bin/env bash

set -e
set -o pipefail

# Variables
REMOTE_SERVER="dannyvankooten_wp@eu1.ibericode.com"
# NGINX serves this directory, see README.md. It only holds the build output:
# rsync --delete removes anything that isn't in dist/
REMOTE_DIR="/var/www/www.dannyvankooten.com"
REMOTE="$REMOTE_SERVER:$REMOTE_DIR/"

# Ensure Git working directory is clean
if [[ -n $(git status --porcelain) ]]; then
    echo "🚨 Deployment aborted: You have uncommitted changes."
    echo "Please commit your changes before deploying to keep Git as the single source of truth."
    exit 1
fi

# build from a clean install, so the output matches package-lock.json
echo "-- building site"
npm ci
npm run build

# deploy to server
echo "-- deploying to $REMOTE"
rsync --recursive --checksum --times --verbose --delete \
	dist/ "$REMOTE"
