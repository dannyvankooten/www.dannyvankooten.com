#!/usr/bin/env bash

set -e

echo "Building site"
gozer -c config_prod.toml build

echo "Minify stylesheet"
minify -o build/styles.css build/styles.css

echo "Optimizing images"
echo "Before: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"
mogrify -sample '1024>' -quality 80 -strip build/**/**/*.{jpg,png}
echo "After: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"

echo "Sending to remote"
rsync -ru build/. rot1.dvk.co:/var/www/dannyvankooten.com --delete
