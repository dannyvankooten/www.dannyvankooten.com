#!/usr/bin/env python3
import requests
from dataclasses import dataclass
from datetime import datetime
from dateutil import parser
import os
import re

@dataclass
class Repo:
    name: str
    desc: str
    url: str
    license: str
    created: datetime
    updated: datetime

def from_github(source: str):
    """Fetch git repositories from GitHub"""
    GITHUB_TOKEN = os.getenv('GITHUB_ACCESS_TOKEN', '')
    if GITHUB_TOKEN == '':
            print("Please set the GITHUB_TOKEN env variable")
            exit()

    repos = []
    res = requests.get('https://api.github.com/' + source + '/repos',
                       headers={
                           'X-GitHub-Api-Version': '2022-11-28',
                           'Authorization': 'Bearer ' + GITHUB_TOKEN,
                           'Accept': 'application/vnd.github+json'
                           }, timeout=10)
    if not res.ok:
        print("GitHub API request failed: " + res.text)
        exit()

    for repo in res.json():
        if repo['archived'] or repo['private'] or repo['fork']:
            continue

        r = Repo(repo['name'], repo['description'], repo['html_url'], '', parser.parse(repo['created_at']),
                          parser.parse(repo['pushed_at']))
        if repo['license']:
            r.license = repo['license']['spdx_id']

        repos.append(r)

    return repos

repos = []
repos += from_github('users/dannyvankooten')
repos += from_github('orgs/ibericode')

# sort list by last activity, most recent first
repos.sort(key=lambda r: r.updated, reverse=True)

now = datetime.now().strftime('%b %d, %Y')
html = f'<p>This page was last updated on {now}.'
for r in repos:
    updated = r.updated.strftime('%b %d, %Y')
    created = r.created.strftime('%b %d, %Y')

    html += '<div>'
    html += f'<h4>{r.name}</h4>'
    html += f'<p>{r.desc}</p>'
    html += '<p><small>'
    html += f'First commit on <time>{created}</time><br />'
    html += f'Last commit on <time>{updated}</time><br />'
    if r.license:
        html += f'Licensed under {r.license}<br />'
    html += f'<a href="{r.url}">{r.url}</a>'
    html += '</small></p>'
    html += '</div>'

# Read in the file
with open('content/code.md', 'r') as file :
      filedata = file.read()

# Replace the target string
filedata = re.sub(r'<!-- GIT_REPOSITORIES_START -->(.|\n)*(?=<!-- GIT_REPOSITORIES_END -->)', f'<!-- GIT_REPOSITORIES_START -->{html}', filedata)

# Write the file out again
with open('content/code.md', 'w') as file:
    file.write(filedata)

