#!/usr/bin/env python

import signal
import sys
import httplib, mimetypes
import requests

#! cd /Applications/MAMP/htdocs/radenium/components/com_radenium/models/python/


SERVER_HOST__                             = "localhost"
SERVER_PORT__                             = 8888
SERVER_API_PATH__                         = "/radenium/publishlive.php"

RADENIUM_API_KEY__                       = "testkey"
RADENIUM_API_UNAME__                       = "student2"

class post_request:
    
    def __init__( self,
                 host = SERVER_HOST__,
                 port = SERVER_PORT__,
                 path = SERVER_API_PATH__,
                 secure = False ):
        
        
        self.secure = secure
        if self.secure:
            self.posturl = "https://" + host + path
        else:
            self.posturl = "http://" + host + ":" + str( port ) + path


    def post(self, files=[], payload={}):
        files_dict = {}
            
        for f in files:
            files_dict = {'file': open(f, 'rb')}

        payload['RADENIUM_API_KEY']   = RADENIUM_API_KEY__
        payload["RADENIUM_API_UNAME"] = RADENIUM_API_UNAME__
        
        r = requests.post(self.posturl.strip(), files=files_dict, data=payload )
        
        return r


class phppublish:
    def __init__(self, config={}):
        print("phppublish created...")
        self.runalways = True
        signal.signal(signal.SIGTERM, self.signal_term_handler)
        signal.signal(signal.SIGINT, self.signal_term_handler)


    def signal_term_handler(self, signal, frame):
        print ('Closed gracefully, have a nice day!')
        self.runalways = False


    def main(self):
        while self.runalways:
            pass


phpub = phppublish()
#phpub.main()
print("Ending phppublish")
sys.exit(0)

