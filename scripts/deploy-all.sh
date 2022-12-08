set -e

echo "Update /code/"
python scripts/repos_to_html.py

echo "Updating download numbers"
python ./scripts/update-wp-downloads.py 

echo "Building Zola site"
zola build

echo "Minify stylesheet"
minify -o public/styles.css public/styles.css

./scripts/optimize-images.sh

echo "Sending to remote"
rsync -ru public/. www.dvk.co:/var/www/dannyvankooten.com --delete
