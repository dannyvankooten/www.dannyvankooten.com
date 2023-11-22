#!/usr/bin/env sh
set -e

echo "Building site"
go run .

echo "Minify stylesheet"
minify -o build/styles.css build/styles.css

echo "Sending to remote"
rsync -ru build/. rot1.dvk.co:/var/www/dannyvankooten.com --delete
