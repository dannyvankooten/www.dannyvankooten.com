#!/usr/bin/env bash

set -e

echo "Update /code/"
./bin/repos_to_html.py

echo "Updating download numbers"
./bin/update-wp-downloads.py


./bin/deploy.sh
