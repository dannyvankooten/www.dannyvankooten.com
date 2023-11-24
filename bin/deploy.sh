#!/usr/bin/env sh
set -e

echo "Building site"
gozer -c config_prod.toml build

echo "Minify stylesheet"
minify -o build/styles.css build/styles.css

echo "Sending to remote"
rsync -ru build/. rot1.dvk.co:/var/www/dannyvankooten.com --delete
