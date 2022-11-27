#!/usr/bin/env bash

set -e

echo "Optimizing images"
echo "Before: $(du -bch public/**/**/*.{jpg,png} | tail -n1)"
mogrify -sample '1024>' -quality 80 -strip public/**/**/*.{jpg,png}
echo "After: $(du -bch public/**/**/*.{jpg,png} | tail -n1)"