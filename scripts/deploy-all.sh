#!/usr/bin/env bash

set -e

echo "Update /code/"
./scripts/repos_to_html.py

echo "Updating download numbers"
./scripts/update-wp-downloads.py 

echo "Building Zola site"
zola build

echo "Minify stylesheet"
lightningcss --minify -o public/styles.css public/styles.css

./scripts/optimize-images.sh

echo "Sending to remote"
rsync -ru public/. www.dvk.co:/var/www/dannyvankooten.com --delete
