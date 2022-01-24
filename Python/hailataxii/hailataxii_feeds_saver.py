#!/usr/bin/env python
# -*- coding: utf-8 -*-

import argparse
import os
import datetime
import libtaxii as t
import libtaxii.messages_11 as tm11
import libtaxii.clients as tc
from libtaxii.common import generate_message_id
from libtaxii.constants import *

from settings import USERNAME, PASSWORD, FEEDS, RESULTING_FOLDER


def main(username, password, feeds, result_folder):
    client = tc.HttpClient()
    client.set_auth_type(tc.HttpClient.AUTH_BASIC)
    client.set_auth_credentials({'username': username, 'password': password})
    client.set_use_https(False)

    for feed in feeds:
        print 'Start extracting `{}` feed ({}/{})'.format(feed, feeds.index(feed)+1, len(feeds))
        poll_request = tm11.PollRequest(generate_message_id(),
                                        collection_name=feed,
                                        subscription_id=generate_message_id())
        poll_xml = poll_request.to_xml(pretty_print=True)
        http_resp = client.call_taxii_service2('hailataxii.com', '/taxii-data', VID_TAXII_XML_11, poll_xml)
        taxii_message = t.get_message_from_http_response(http_resp, poll_request.message_id)

        result_file = os.path.join(result_folder,
                                   '{}_{}.xml'.format(feed, str(datetime.datetime.now()).replace(' ', '_').replace(':', '_').replace('.', '_')))
        with open(result_file, 'w') as f:
            f.write(taxii_message.to_xml(pretty_print=True))
        print '`{}` feed extracted successfully!'.format(feed)


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Downloader for Hailataxii.com feeds.')
    parser.add_argument("--directory", "-d", help="save feeds path", default=RESULTING_FOLDER)
    parser.add_argument("--username", "-u", help="Hailataxii.com username", default=USERNAME)
    parser.add_argument("--password", "-p", help="Hailataxii.com password", default=PASSWORD)
    parser.add_argument("--feed", "-f", help="Needed feed(s). Comma-separated if more than one.")
    args = parser.parse_args()
    username_arg = args.username
    password_arg = args.password
    result_folder_arg = args.directory

    # create dir if not exists
    if not os.path.exists(result_folder_arg):
        os.makedirs(result_folder_arg)

    feeds_arg = args.feed.split(',') if args.feed else None or FEEDS or []

    main(username=username_arg, password=password_arg, feeds=feeds_arg, result_folder=result_folder_arg)



