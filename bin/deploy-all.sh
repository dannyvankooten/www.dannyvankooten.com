#!/usr/bin/env bash

set -e

echo "Update /code/"
./bin/repos_to_html.py

echo "Updating download numbers"
./bin/update-wp-downloads.py

echo "Building site"
gozer

echo "Minify stylesheet"
minify -o build/styles.css build/styles.css

./bin/optimize-images.sh

echo "Sending to remote"
rsync -ru build/. rot1.dvk.co:/var/www/dannyvankooten.com --delete
