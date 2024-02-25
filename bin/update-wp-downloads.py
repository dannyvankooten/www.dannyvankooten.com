#!/usr/bin/env python3

import requests 
import re
import time

def human_format(num):
    num = float('{:.3g}'.format(num))
    magnitude = 0
    while abs(num) >= 1000:
        magnitude += 1
        num /= 1000.0
    return '{}{}'.format('{:f}'.format(num).rstrip('0').rstrip('.'), ['', 'K', 'M', 'B', 'T'][magnitude])

plugins = [
    "mailchimp-for-wp",
    "koko-analytics",
    "html-forms",
    "boxzilla",
    "mailchimp-top-bar",
    "wysiwyg-widgets",
    "dvk-social-sharing"
]

with open('content/wordpress-plugins.md', 'r') as file :
    contents = file.read()

    for p in plugins:
        time.sleep(0.1)
        response = requests.get(f"https://api.wordpress.org/plugins/info/1.0/{p}.json").json()
        n = human_format(response['downloaded'])
        print(f"{p}: {n} downloads")
        contents = re.sub(r'<td id="' + p + '-downloads">(.+)</td>', f"<td id=\"{p}-downloads\">{n}</td>", contents)


with open('content/wordpress-plugins.md', 'w') as file:
  file.write(contents)