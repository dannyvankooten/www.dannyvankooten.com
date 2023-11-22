#!/usr/bin/env python

import requests
from bs4 import BeautifulSoup
import os
import csv
import time
import subprocess

def write_to_file(path, content):
    """Writes the given string to the given filename"""
    with open(path, "wb") as file:
        file.write(content)

def get_absolute_url(url, domain):
    """Turns the given URL (from a script[src] or link[href] attribute) into an absolute URL, to be used in an HTTP request"""
    url = url.lower()

    if url.startswith('http'):
        return url

    if url.startswith('//'):
        return 'http:' + url
    
    if not url.startswith('/'):
        url = "/" + url
            
    url = "http://" + domain + url
    return url

def get_filesize(f):
    """Returns the size of the given file in bytes if it exists, or 0 if it doesn't"""
    if os.path.exists(f):
        return os.path.getsize(f)
    return 0

def minify_and_gzip(f):
    """Minifies and GZIP's the given file. Returns the amount of bytes saved"""
    print("\tMinifying {}".format(f))
    
    # skip empty files
    if get_filesize(f) == 0:
        return 0

    if f.endswith('.html'):
        process = subprocess.run('html-minifier --collapse-whitespace --remove-comments  --minify-css true --minify-js true  --remove-script-type-attributes --remove-redundant-attributes {} -o {}.min'.format(f, f), errors=True, cwd=os.getcwd(), shell=True)
    elif f.endswith('.css'):
        process = subprocess.run('lightningcss --minify --targets \'>= 0.25%\' {} -o {}.min'.format(f, f), errors=True, cwd=os.getcwd(), shell=True)
    elif f.endswith('.js'):
        process = subprocess.run('terser {} --compress --mangle -o {}.min'.format(f, f), errors=True, cwd=os.getcwd(), shell=True)
    else:
        raise Exception("Invalid file argument to minify_and_gzip")

    # check status of minify command
    if process.returncode != 0:
        print("\tError minifying: {}".format(process.stderr))
        return 0

    # gzip original & minified file
    if os.system("gzip -c {} > {}.gz".format(f, f)) != 0:
        return 0

    if os.system("gzip -c {}.min > {}.min.gz".format(f, f)) != 0:
        return 0

    # calculate size difference
    size_before = get_filesize(f + ".gz")
    size_after = get_filesize(f + ".min.gz")
    savings = size_before - size_after
    print("\t{} -> {} = {}".format(size_before, size_after, savings))
    return savings


def download(url, content_type = 'text/html'):
    print("\tDownloading {}".format(url))

    headers = {
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.7,nl;q=0.3',
        'Accept-Encoding': 'gzip, deflate, br',
        'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64; rv:107.0) Gecko/20100101 Firefox/107.0',
    }
    res = requests.get(url, headers, timeout=10)
    return res
    
def run():
    top_1m_file = open('top-1k.csv')
    top_1m = csv.reader(top_1m_file)
    results_file = open('results.csv', 'a')
    results = csv.writer(results_file)

    for row in top_1m:
        rank = row[0]
        domain = row[1]
        path = "download/{}-{}".format(rank, domain)
        print("-----\n{}".format(domain))

        if os.path.exists(path):
            print("\tDownload path exists. Skipping.")
            continue

        # create data directory for this domain
        # we create it before even performing any HTTP requests
        # so that this domain is not requested again the next time the script runs
        os.mkdir(path)

        url = "http://" + domain
        req = download(url)
        if not req.ok:
            print("\tInvalid response. Skipping.")
            continue

        # write /index.html
        write_to_file(path + "/index.html", req.content)
        html_savings = minify_and_gzip(path + "/index.html")

        # parse HTML 
        uses_gzip = 'Content-Encoding' in req.headers and 'gzip' in req.headers['Content-Encoding']
        soup = BeautifulSoup(req.content, 'html.parser')

        # Download stylesheets
        css_savings = 0
        for i, stylesheet in enumerate(soup.find_all(rel="stylesheet")):
            if not stylesheet.has_attr('href') or stylesheet['href'].startswith('data'):
                continue

            url = get_absolute_url(stylesheet['href'], domain)
            res = download(url)
            if res.ok:
                # write to file
                filename = path + "/" + str(i) + ".css"
                write_to_file(filename, res.content)
                css_savings += saviminify_and_gzip(filename)ngs
        print("\tTotal CSS savings: {}".format(css_savings))

        # Download scripts
        js_savings = 0
        for i, script in enumerate(soup.find_all('script')):
            if not script.has_attr('src') or script['src'].startswith('data'):
                continue
            
            url = get_absolute_url(script['src'], domain)
            res = download(url)
            if res.ok:
                filename = path + "/" + str(i) + ".js"
                write_to_file(filename, res.content)
                js_savings += minify_and_gzip(filename)
        print("\tTotal JS savings: {}".format(js_savings))
        print("\tCombined savings: {}".format(html_savings + js_savings + css_savings))

        # Compare sizes and write results to CSV 
        results.writerow([domain, html_savings, js_savings, css_savings])

    # close open files
    results_file.close()
    top_1m_file.close()

if __name__ == '__main__':
    run()