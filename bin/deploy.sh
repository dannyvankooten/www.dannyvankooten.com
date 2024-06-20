#!/usr/bin/env bash
#
# Usage: ./bin/deploy.sh (updates all)
# Usage: ./bin/deploy.sh fast (skips remote content)

set -e
set -o pipefail

if [[ "$1" != "fast" && "$1" != "--fast" ]]; then
    echo "Update /code/"
    ./bin/repos_to_html.py

    echo "Updating download numbers"
    ./bin/update-wp-downloads.py
fi

echo "Building site"
gozer --config=config_prod.toml build

# See https://developers.google.com/speed/docs/insights/OptimizeImages
IMAGES=$(find build/ -type f \( -iname '*.jpg' -o -iname '*.png' -o -iname '*.gif' -o -iname '*.jpeg' -o -iname '*.webp' \))
echo "Optimizing images"
echo "Before: $(du -bch $IMAGES | tail -n1)"
find build/ -type f \( -iname '*.jpg' -o -iname '*.jpeg' \) -exec mogrify -strip -sampling-factor 4:2:0 -resize 1024x\> -quality 85 -interlace JPEG -colorspace sRGB {} \;
find build/ -type f \( -iname '*.png' -o -iname '*.gif' \) -exec mogrify -strip -resize 1024x\> -alpha Remove {} \;
echo "After: $(du -bch $IMAGES | tail -n1)"

echo "Sending to remote"
rsync -rzav --no-owner --no-perms --no-group build/. danny@eu1.ibericode.com:/var/www/www.dannyvankooten.com/


