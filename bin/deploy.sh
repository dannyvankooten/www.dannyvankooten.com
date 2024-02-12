#!/usr/bin/env bash
#
# Usage: ./bin/deploy.sh (updates all)
# Usage: ./bin/deploy.sh fast (skips remote content)

set -e

if [[ "$1" != "fast" && "$1" != "--fast" ]]; then
    echo "Update /code/"
    ./bin/repos_to_html.py

    echo "Updating download numbers"
    ./bin/update-wp-downloads.py
fi

echo "Building site"
gozer -c config_prod.toml build

echo "Optimizing images"
echo "Before: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"
mogrify -sample '1024>' -quality 90 -strip build/**/**/*.{jpg,png}
echo "After: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"

echo "Sending to remote"
tar -C build -cvz . > site.tar.gz
hut pages publish -d www.dannyvankooten.com site.tar.gz
#hut pages publish -d dvko.srht.site site.tar.gz

