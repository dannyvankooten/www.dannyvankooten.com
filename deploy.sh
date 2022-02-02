echo "Updating download numbers"
python ./scripts/update-wp-downloads.py 

echo "Building Jekyll site"
bundle exec jekyll build --config=_config.yml,_config_prod.yml

echo "Optimizing images"
echo "Before: $(du -h _site/media | tail -n1)"
mogrify -sample '1024>' -quality 82 -strip _site/**/**/*.jpg 
for f in _site/**/**/*.jpg; do
	jpegtran -optimize -perfect -outfile "$f" $f
done

optipng -quiet _site/**/**/*.png
echo "After: $(du -h _site/media | tail -n1)"

echo "Sending to remote"
rsync -ru _site/. www.dvk.co:/var/www/dannyvankooten.com --delete
