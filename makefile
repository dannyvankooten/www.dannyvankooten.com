.PHONY: deploy
deploy:
	bundle exec jekyll build
	rsync -ru _site/. rico-ams1:/var/www/dannyvankooten.com --delete
