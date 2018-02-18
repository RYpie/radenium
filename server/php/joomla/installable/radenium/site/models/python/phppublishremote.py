#!/usr/bin/env python

import signal
import sys, os
import httplib, mimetypes
import requests
import time

#! cd /Applications/MAMP/htdocs/radenium/components/com_radenium/models/python/


class pids:
    def check_pid(self, pid):        
        """ Check For the existence of a unix pid. """
        try:
            os.kill(pid, 0)
        except OSError:
            return False
        else:
            return True

    def pid_running(self, pid):
        """Check whether pid exists in the current process table.
        UNIX only.
        """
        if pid < 0:
            return False
        
        if pid == 0:
            # According to "man 2 kill" PID 0 refers to every process
            # in the process group of the calling process.
            # On certain systems 0 is a valid PID but we have no way
            # to know that in a portable fashion.
            raise ValueError('invalid PID 0')
        try:
            os.kill(pid, 0)
        
        except OSError as err:
            if err.errno == errno.ESRCH:
                # ESRCH == No such process
                return False
            
            elif err.errno == errno.EPERM:
                # EPERM clearly means there's a process to deny access to
                return True
            
            else:
                # According to "man 2 kill" possible error values are
                # (EINVAL, EPERM, ESRCH)
                raise
            
        else:
            return True


class post_request:
    def __init__( self, url, options={} ):
        #! todo Should do some url checking here.
        self.posturl = url
        self.options = options

    def post(self, files=[], payload={}):
        files_dict = {}
            
        for f in files:
            files_dict = {'file': open(f, 'rb')}
        
        r = requests.post(self.posturl.strip(), files=files_dict, data=payload )
        
        return r

m3u8_directories={
'joomla':'media/com_radenium/media/takes/id_'
, 'python':'../../../../media/com_radenium/media/takes/id_'
}

class phppublish:
    """ Publishes an HLS stream to phplive.php, requires command line arguments.
        -id string Stream id of the stream to publish, is added to the m3u8_directories item to obtain the right directory.
        -caller string Indicates if the script is called by the joomla system or by python, if python than it should be called from the model directory, for debugging purposes.
        -pid string process id of the running take, the idea was to monitor that process id, if it stops, than also stop this process.
        """
    def __init__(self, config={}):
        #print("phppublish created...")
        signal.signal(signal.SIGTERM, self.signal_term_handler)
        signal.signal(signal.SIGINT, self.signal_term_handler)
        self.handle_pid = None
        
        argcount=0
        #print(config)
        
        #! Initialize the directory with a reference as if this file was called by hand from the model directory.
        self.m3u8_dir = m3u8_directories['python']
        for arg in config:
            if arg == '-id':
                #! To obtain the proper stream from this system.
                self.id = config[argcount + 1]
                
            elif arg == '-caller':
                self.m3u8_dir = m3u8_directories[config[argcount + 1]]
                
            elif arg == '-pid':
                self.handle_pid = config[argcount + 1]
            
            elif arg == '-url':
                self.url = config[argcount + 1]
            
            elif arg == '-uname':
                self.uname = config[argcount + 1]
            
            elif arg == '-ukey':
                self.ukey = config[argcount + 1]
            
            argcount += 1
            
        self.m3u8_dir += str(self.id)
        self.m3u8_file = self.m3u8_dir + "/playlist.m3u8" #livedeck.getM3u8File()
        print( self.m3u8_dir, self.m3u8_file)
        #print(os.getcwd(), "publishing ID:"+self.id, self.m3u8_file)
        
        self.runalways = True
        self.m3u8tool = m3u8()
        #! Keep track of the publishing status in self.publishStatus.
        self.publishStatus = {}
        self.publishStatus['m3u8_now'] = []
        self.publishStatus['m3u8_prev'] = []
        self.post = post_request(self.url)
    
        
        
    def signal_term_handler(self, signal, frame):
        #print ('Closed gracefully, have a nice day!')
        print ("Closing gracefully, one moment please...")
        self.runalways = False

    def get_m3u8(self):
        
        if os.path.isfile( self.m3u8_file ):
            f = open(self.m3u8_file,'r')
            m3u8_text = f.read()
            f.close()
            m3u8_data = self.m3u8tool.parse_m3u8(m3u8_text)
            
            return m3u8_data
            
        else:
            return None

            
    def getPostPayload(self, type='hls_update'):
        ''' Prepares a dictionary with data to be posted to the api server.
        '''
        retVal = {'task':type}
        retVal['stream_id'] = self.id
        retVal['uname'] = self.uname
        retVal['ukey'] = self.ukey
        
        if type=='hls_update':
            pass
        
        elif type == 'announce_live_start':
            retVal['preroll'] = 'default'
        
        elif type == 'announce_live_stop':
            retVal['postroll'] = 'default'
        
        elif type == 'live_metadata':
            #post information during the program
            pass
    
        return retVal
        
        
    def init_hls(self,mode):
        """ todo Create a json file with channel information out of Joomla.
            """
        if mode == "start":
            print self.post.post([], self.getPostPayload('announce_live_start')).text

        if mode == "stop":
            print self.post.post([], self.getPostPayload('announce_live_stop')).text


    def post_hls(self):
        m3u8_data = self.get_m3u8()
        if m3u8_data != None:
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
        #! Initialize the hls stream to start at the php side.
        self.init_hls(mode="start")
        while self.runalways:
            self.post_hls()
            time.sleep(1)
            #self.runalways = False
            #! Should check for self.handle_pid because that is the pid of the livestreaming process

        #! Initialize the hls stream to stop at the php side. Basically, cleans up everything.
        self.init_hls(mode="stop")
        # sys.exit(0)
        
        
class m3u8:
    """A class to support the hls update functionality."""
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
print("python phppublishremote.py -id 188 -caller python -pid 65813 -url http://localhost:8888/radlive -uname student32 -ukey testkey")
phpub = phppublish(config=sys.argv)
phpub.main()
#print("Ending phppublish")
sys.exit(0)

