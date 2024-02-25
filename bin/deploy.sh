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
gozer --config=config_prod.toml build

echo "Optimizing images"
echo "Before: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"
mogrify -strip -sampling-factor 4:2:0 -resize 1024x\> -quality 85 -interlace JPEG -colorspace sRGB build/**/**/*.jpg
mogrify -strip -sampling-factor 4:2:0 -resize 1024x\> -quality 85 -interlace JPEG -colorspace sRGB build/**/*.jpg
mogrify -strip -resize 1024x\> -alpha Remove build/**/**/*.png
mogrify -strip -resize 1024x\> -alpha Remove build/**/*.png
mogrify -strip -resize 1024x\> build/**/*.gif
echo "After: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"

echo "Sending to remote"
rsync -rav build/. dannyvankooten@1.dannyvankooten.com:/var/www/www.dannyvankooten.com/

#tar -C build -cvz . > site.tar.gz
#hut pages publish -d www.dannyvankooten.com site.tar.gz
#hut pages publish -d dvko.srht.site site.tar.gz

