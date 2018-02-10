#!/usr/bin/env python

import signal
import sys, os
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
        #print("phppublish created...")
        signal.signal(signal.SIGTERM, self.signal_term_handler)
        signal.signal(signal.SIGINT, self.signal_term_handler)
        argcount=0
        for arg in config:
            if arg == '-id':
                self.id = config[argcount + 1]
            
            argcount += 1
            
        self.runalways = True
        self.m3u8tool = m3u8()
        self.m3u8_dir = "../../../../media/com_radenium/media/takes/id_" + str(self.id)
        self.m3u8_file = self.m3u8_dir + "/playlist.m3u8" #livedeck.getM3u8File()
        
        #print(os.getcwd(), "publishing ID:"+self.id, self.m3u8_file)
        
        #! Keep track of the publishing status in self.publishStatus.
        self.publishStatus = {}
        self.publishStatus['m3u8_now'] = []
        self.publishStatus['m3u8_prev'] = []
        self.post = post_request()
        
        print( self.get_m3u8() )
        self.post_hls()
        
        
    def signal_term_handler(self, signal, frame):
        #print ('Closed gracefully, have a nice day!')
        self.runalways = False

    def get_m3u8(self):
        if os.path.isfile( self.m3u8_file ):
            f = open(self.m3u8_file,'r')
            m3u8_text = f.read()
            f.close()
            m3u8_data = self.m3u8tool.parse_m3u8(m3u8_text)
            
            return m3u8_data
            
        else:
            return False
            
    def getPostPayload(self, type='hls_update'):
        ''' Prepares a dictionary with data to be posted to the api server.
        '''
        retVal = {'task':type}
        retVal['stream_id'] = self.id
        
        if type=='hls_update':
            retVal['scheduled'] = '';
        elif type == 'announce_live':
            retVal['preroll'] = 'default';
        elif type == 'announce_live_stop':
            retVal['postroll'] = 'default';
        elif type == 'live_metadata':
            #post information during the program
            pass
        elif type == 'update_system_devices':
            retVal['RADENIUM_API_INPUT_DEVICES_JSON'] = ""
        
        return retVal
        
    def post_hls(self):
        m3u8_data = self.get_m3u8()
        newFilesUploaded = False
        for file in m3u8_data['files']:
            upload = False
            #print "Volgende file gevonden: "+ file['name']
                #! Trying to fetch the name from self.publishStatus, if its in there it is already uploaded.
            if file['name'] in self.publishStatus['m3u8_prev']:
                print( ">>> Fille already uploaded <<<" )
                pass
                
            else:
                print( "Uploading the " + file['name'] + "...")
                files = []
                files.append( self.m3u8_dir + '/' + file['name'] )
                #print "\nDoing the post..."
                print( "POST IT NOW" )
                print self.post.post(files,self.getPostPayload('hls_update')).text
                
                self.publishStatus['m3u8_prev'].append( file['name'] )
                newFilesUploaded = True
                
        if newFilesUploaded:
            #! Update the m3u8 file
            print( "Update the remote m3u8 file" )
            print self.post.post([self.m3u8_file], self.getPostPayload('hls_update')).text
            
            
            
    def main(self):
        while self.runalways:
            pass
            
        sys.exit(0)
        
        
class m3u8:
    def parse_m3u8(self, m3u8_text):
        #print m3u8_text
        m3u8 = {}
        old_stream = False
        m3u8['files']=[]
        m3u8['closed'] = False
        m3u8_lines = m3u8_text.split('\n')
        lineCounter = 0
        file = {}
        for line in m3u8_lines:
            if '#EXT-X-ENDLIST' in line:
                #! Then this is an old file and we are not livestreaming yet.
                m3u8['closed'] = True
                
            if '#EXTINF:' in line:
                file['extinf'] = line
                file['name'] = m3u8_lines[lineCounter+1]
                m3u8['files'].append(file.copy())
                file = {}
                
            lineCounter += 1
            
        return m3u8
        
        
    def putm3u8file(self, file_location, contents):
        f = open(file_location,'w')
        m3u8_text = f.write( contents )
        f.close()
        
        
    def getm3u8file(self, file_location ):
        print file_location
        f = open(file_location,'r')
        m3u8_text = f.read()
        f.close()
        
        return m3u8_text
        
    def closem3u8file(self, file_location):
        m3u8 = self.getm3u8file(file_location)
        m3u8_c = m3u8.split('\n')
        alreadyClosed = False
        for line in m3u8_c:
            if '#EXT-X-ENDLIST' in line:
                alreadyClosed = True
                
        if not alreadyClosed:
            m3u8 += '\n#EXT-X-ENDLIST\n'
            self.putm3u8file(file_location, m3u8)
        
        print m3u8
    


print("Starting PHP Publisher")

phpub = phppublish(config=sys.argv)
#phpub.main()
#print("Ending phppublish")
sys.exit(0)

