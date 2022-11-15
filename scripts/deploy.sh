echo "Updating download numbers"
python ./scripts/update-wp-downloads.py 

echo "Building Zola site"
zola build

echo "Minify stylesheet"
minify -o public/styles.css public/styles.css

echo "Optimizing images"
echo "Before: $(du -h public/media | tail -n1)"
mogrify -sample '1024>' -quality 82 -strip public/**/**/*.jpg 
for f in public/**/**/*.jpg; do
	jpegtran -optimize -perfect -outfile "$f" $f
done

optipng -quiet public/**/**/*.png
echo "After: $(du -h public/media | tail -n1)"

echo "Sending to remote"
rsync -ru public/. www.dvk.co:/var/www/dannyvankooten.com --delete
