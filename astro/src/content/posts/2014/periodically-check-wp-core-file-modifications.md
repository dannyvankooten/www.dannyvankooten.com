---
title: "Periodically check WordPress core for (malicious) modifications"
description: "Using WP-CLI's verify-checksums command to detect unauthorized modifications to WordPress core files."
date: "2014-10-11T00:00:00+00:00"
modified: "2026-05-13T14:56:04+02:00"
categories: ["WordPress"]
---

Last Thursday I was at a WordPress meetup where [Jaime Martinez](https://www.jaimemartinez.nl/) did a talk about [WP-CLI](https://wp-cli.org/), a command line interface for WordPress.

One of the very useful commands he showed is `wp core verify-checksums`, which verifies the checksum of all WordPress core files. It will tell you exactly which of your core files do not match the original files in the WordPress.org repository.

```
$ wp core verify-checksums
Warning: File does not verify against checksum: wp-admin/about.php
Warning: File does not verify against checksum: wp-load.php
Error: WordPress install does not verify against checksums.
```

Running this command at a set interval can be very useful to monitor your WordPress installations for malicious modifications.

I configured a cronjob on my server to run the following script every day.

```
#!/bin/bash
cd /my/wp/directory

if [ -e /tmp/wp-core-verify-checksums-notified ]; then
    exit
fi

if [ ! $(wp core verify-checksums) ]; then
    curl https://api.pushbullet.com/api/pushes \
          -u MY_API_KEY: \
          -d type=note \
          -d title="WP checksum verification failed." \
          -d body="Checksum of your WP core files does not match the checksum of original core files. Inspect the modified files." \
          -X POST

    touch /tmp/wp-core-verify-checksums-notified
fi
```

It traverses into my WordPress directory and runs the WP-CLI command. If verification failed it will send a push notification to all my devices using [Pushbullet](https://www.pushbullet.com/).

If you ask me it is definitely worth a few minutes of your time to configure something along the same lines for your server(s) or customers.

Suggestions and improvements are welcome.
