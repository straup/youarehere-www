#!/usr/bin/env python

# THIS IS NOT A PROPER TESTING FRAMEWORK - it's just
# a tool for doing simple sanity checking (20130406/straup)

import sys
import optparse
import ConfigParser

import logging

import youarehere.api.client

if __name__ == '__main__':

    parser = optparse.OptionParser()
    parser.add_option('--config', dest='config', action='store', help='...', default=None)
    parser.add_option("-v", "--verbose", dest="verbose", action="store_true", help="enable chatty logging; default is false", default=False)

    (opts, args) = parser.parse_args()

    if opts.verbose:
        logging.basicConfig(level=logging.DEBUG)
    else:
        logging.basicConfig(level=logging.INFO)

    cfg = ConfigParser.ConfigParser()
    cfg.read(opts.config)

    #

    token = cfg.get('api', 'access_token')
    host = cfg.get('api', 'host')

    # Mostly stuff for doing development - this isn't
    # anything that you should need to care about once
    # the API is live and public (20130403/straup)

    kwargs = {}

    for opt in ('username', 'password', 'use_https'):
        
        if cfg.has_option('api', opt):

            if opt == 'use_https':
                kwargs[opt] = bool(int(cfg.get('api', opt)))
            else:
                kwargs[opt] = cfg.get('api', opt)
    
    # put me in a file or something...
    
    tests = [
        [ 'api.spec.formats', {} ],
        [ 'api.spec.methods', {} ],
        [ 'api.test.echo', {'foo': 'bar' } ],
        [ 'api.test.error', {} ],
        [ 'youarehere.assertions.getAssertionsByDate', { 'start_date': '2013-04-01', 'end_date': '2013-04-02' } ],
        [ 'youarehere.assertions.perspectives.getList', {} ],
        [ 'youarehere.geo.geocode', { 'query': 'melbourne' } ],
        [ 'youarehere.geo.reverseGeocode', { 'lat': 40.683789, 'lon': -73.989958, 'filter': 'localities' } ],
        [ 'youarehere.geo.filters.getList', {} ],
        [ 'youarehere.geo.sources.getList', {} ],
        ]

#    tests = [
#        [ 'youarehere.assertions.assertLocation', { 'lat': 40.674780, 'lon': -73.997705, 'woe_id': 18807771, 'perspective_id': 1} ],
#        [ 'youarehere.assertions.deleteAssertion', { 'assertion_id': 51551745 } ],
#        ]

    #

    api = youarehere.api.client.OAuth2(token, host, **kwargs)

    for method, args in tests:

        logging.info("testing %s w/ %s" % (method, args))

        try:
            rsp = api.call(method, **args)
            logging.debug(rsp)
        except Exception, e:
            logging.error("API error: %s" % e)
            continue

        if rsp['stat'] != 'ok' and method != 'test.error':
            logging.error("API error: %s" % rsp)
            continue

        logging.info("%s OK" % method)

    #

    logging.info("done")
    sys.exit()
