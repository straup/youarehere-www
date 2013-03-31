#!/usr/bin/env python

import sys
import optparse
import ConfigParser

import csv
import logging

import youarehere.api

if __name__ == '__main__':

    parser = optparse.OptionParser()
    parser.add_option('--ymd', dest='ymd', action='store', help='...', default=None)
    parser.add_option('--config', dest='config', action='store', help='...', default=None)
    parser.add_option('--output', dest='output', action='store', help='..., default=None')
    parser.add_option("-v", "--verbose", dest="verbose", action="store_true", help="enable chatty logging; default is false", default=False)

    (opts, args) = parser.parse_args()

    if opts.verbose:
        logging.basicConfig(level=logging.DEBUG)
    else:
        logging.basicConfig(level=logging.INFO)

    cfg = ConfigParser.ConfigParser()
    cfg.read(opts.config)

    #

    fh = sys.stdout

    if opts.output:
        fh = open(opts.output, 'w')

    fieldnames = ['id', 'woe_id', 'latitude', 'longitude', 'perspective', 'created' ]

    writer = csv.DictWriter(fh, fieldnames=fieldnames)
    writer.writeheader()

    #

    token = cfg.get('api', 'access_token')
    endpoint = cfg.get('api', 'endpoint')

    yah = youarehere.api.API(token, endpoint)

    args = {
        'start_date' : "%s 00:00:00" % opts.ymd,
        'end_date' : "%s 23:59:59" % opts.ymd,
        }

    page = 1
    pages = None

    while not pages or page <= pages:

        logging.debug("fetching page %s" % page)

        args['page'] = page

        try:
            rsp = yah.call('youarehere.corrections.getCorrectionsByDate', **args)
        except Exception, e:
            logging.error("API error: %s" % e)
            break

        if rsp['stat'] != 'ok':
            logging.error("API error: %s" % rsp)
            break

        if not pages:
            pages = rsp['pages']
            logging.debug("total pages: %s" % pages)

        if pages == 0:
            logging.info("no results for that date")
            break

        for f in rsp['features']:
            
            row = f['properties']

            row['latitude'] = f['geometry']['coordinates'][1]
            row['longitude'] = f['geometry']['coordinates'][0]
            row['id'] = f['id']

            writer.writerow(row)

        page += 1

    logging.info("done")
    sys.exit()
