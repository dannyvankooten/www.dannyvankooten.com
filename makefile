.PHONY: deploy
deploy:
	bundle exec jekyll build
	rsync -ru _site/. www.dvk.co:/var/www/dannyvankooten.com --delete
