#/usr/bin/env bash
#
# This just keeps running main.py and restarts it in case it exits because of an error

until ./main.py; do
    echo "script crashed with exit code $?.  Respawning.." >&2
    sleep 1
done
