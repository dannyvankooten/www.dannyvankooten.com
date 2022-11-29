#!/usr/bin/env python

import requests
import re 
import os
import time
import csv
import email.utils as eut
from bs4 import BeautifulSoup
from main import get_absolute_url, download
from datetime import datetime

rx_max_age = re.compile(r"max-age[=:] ?(\d*)")

def parse_http_date(text):
    return datetime(*eut.parsedate(text)[:6])

def parse_cache_header(headers):
    # Cache-Control takes importance over Expires
    # So inspect that first
    if 'Cache-Control' in headers:
        if 'max-age' in headers['Cache-Control']:
            m = rx_max_age.search(headers['Cache-Control'])
            if m and m.group(1) != "":
                return int(m.group(1))

        return 0
    elif 'Expires' in headers:
        expires_date = parse_http_date(headers['Expires'])
        if expires_date:
            return max(0, round((expires_date - datetime.now()).total_seconds()))
    
    return 0

with open('results.csv','r') as csvinput:
    with open('output.csv', 'w') as csvoutput:
        writer = csv.writer(csvoutput)
        results = csv.reader(csvinput)

        # write header row
        header = next(results)
        if len(header) < 7:
            header.append("expires_2")
        writer.writerow(header)

        # loop through domains
        for row in results:
            # already seen this 
            if len(row) >= 7:
                writer.writerow(row)
                continue

            # download homepage
            domain = row[0]
            url = "http://" + domain
            try:
                req = download(url)
                if not req.ok:
                    print("\tInvalid response while downloading homepage. Skipping.")
                    continue
            except:
                print("\tException occurred during request. Skipping.")
                continue

            # parse HTML 
            soup = BeautifulSoup(req.content, 'html.parser')
            scripts = list(filter(lambda el: el.has_attr('src') and not el['src'].startswith('data'), soup.find_all("script")))
            scripts += list(filter(lambda el: el.has_attr('href') and not el['href'].startswith('data'), soup.find_all(rel="stylesheet")))
            lifetimes = []
            for script in scripts:
                # Get URL from element
                if script.has_attr('src'):
                    url = script['src']
                else:
                    url = script['href']
                url = get_absolute_url(url, domain)

                # Try downloading asset
                try:
                    response = download(url)
                    if not response.ok:
                        continue 
                except:
                    print("\tException occurred during request. Skipping.")
                    continue
                
                # Parse cache lifetime header
                expires = parse_cache_header(response.headers)
                lifetimes.append(expires)
                print("\tFound Cache-Control or Expires header of {} seconds".format(expires))
               
                # Sleep for tiny bit so we're not firewalled
                time.sleep(0.05)

            if len(lifetimes) > 0:
                avg = int(sum(lifetimes) / len(lifetimes))
            else:
                avg = ""

            row.append(avg)
            writer.writerow(row)


