#!/usr/bin/env bash
#
# Usage: ./bin/deploy.sh (updates all)
# Usage: ./bin/deploy.sh fast (skips remote content)

set -e
set -o pipefail

npm run build
rsync --recursive --checksum --times --verbose --delete dist/ danny@eu1.ibericode.com:/var/www/www.dannyvankooten.com/


