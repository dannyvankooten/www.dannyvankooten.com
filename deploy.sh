bundle exec jekyll build --config=_config.yml,_config_prod.yml
rsync -ru _site/. www.dvk.co:/var/www/dannyvankooten.com --delete