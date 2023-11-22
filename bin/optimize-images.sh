#!/usr/bin/env bash

set -e

echo "Optimizing images"
echo "Before: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"
mogrify -sample '1024>' -quality 80 -strip build/**/**/*.{jpg,png}
echo "After: $(du -bch build/**/**/*.{jpg,png} | tail -n1)"
