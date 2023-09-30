#!/usr/bin/env sh 
set -e

echo "Building Zola site"
zola build

echo "Minify stylesheet"
minify -o public/styles.css public/styles.css

echo "Tidying HTML"
find public/. -name "*.html" -type f -print -exec tidy -q -i -m --tidy-mark no {} \;

echo "Sending to remote"
rsync -ru public/. www.dvk.co:/var/www/dannyvankooten.com --delete
