#!/usr/bin/env sh
set -e

echo "Building Zola site"
zola build

echo "Minify stylesheet"
minify -o public/styles.css public/styles.css

echo "Sending to remote"
rsync -ru public/. rot1.dvk.co:/var/www/dannyvankooten.com --delete
