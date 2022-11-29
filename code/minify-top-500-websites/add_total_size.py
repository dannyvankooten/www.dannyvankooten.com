#!/usr/bin/env python

import csv
import glob 
from main import get_filesize

with open('results.csv','r') as csvinput:
    with open('output.csv', 'w') as csvoutput:
        writer = csv.writer(csvoutput)
        results = csv.reader(csvinput)

        # add header column
        header = next(results)
        header.append("total_size")
        writer.writerow(header)

        # loop through domains
        for row in results:
            domain = row[0]

            dir = glob.glob('download/*-{}'.format(domain))[0]
            files = [ dir + '/index.html.gz' ] + glob.glob(dir + '/*.js.gz') + glob.glob(dir + '/*.css.gz')
            total_size = sum([get_filesize(f) for f in files])

            if total_size > 0:
                row.append(total_size)
            else:
                row.append("")
                        
            writer.writerow(row)

