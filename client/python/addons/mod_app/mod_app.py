#!/usr/bin/env python3
# -*- coding: utf-8 -*-


"""
    Copyright 2017 Andries Bron
    This file is part of Radenium.
    
    Radenium is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    Radenium is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with Radenium.  If not, see <http://www.gnu.org/licenses/>.
    """

'''
    Something with signals...
import signal
import sys

def signal_term_handler(signal, frame):
    print ('got SIGTERM')
    print ('Thats it baby')
    sys.exit(0)



if __name__ == "__main__":
    pass
    signal.signal(signal.SIGTERM, signal_term_handler)
    #! signal.signal(signal.SIGINT, signal_term_handler)
    #! https://nattster.wordpress.com/2013/06/05/catch-kill-signal-in-python/
    while True:
        pass
    #! sys.exit(0)
    print ('Thats it baby')

'''

import threading
import queue
import time
import datetime
import sys


sys.path.append('../mod_dbaccess')
try:
    import mod_dbaccess

except:
    pass


class mod_app(threading.Thread):
    ''' Base class for a threaded app build in Radenium
    This base class is extended by an app in Radenium to enable other apps to interact with each other. Any app in Radenium should be run autonomous as well so that they can be used in applications other than Radenium.
    The application base class has an inqueue that accepts anything. However, dictionaries with primary keys "task" and "event" are preserved with a default structure.
    The application base class also has an interface to a database which should not be required to use the application into any other application. Database interaction therefore should always be for the sake of clean input or output but not support functionality. Functionality is consequently a task of the entity handling the in and output of a Radenium application.
    '''
    def __init__(self, runCB=None, outqueue=None, sleeptime=1000):
        """ Constructor.
        """
        super().__init__()
        self.runAlways = False
        self.sleeptime = sleeptime
        self.outqueue = outqueue
        self.inqueue=queue.Queue()
        #! Announcing a reference to the inqueue of this class.
        self.pushEvent({"queue":{"dir":"in","ref":self.inqueue, "name":self.name()}})
        self.runCB = runCB
        try:
            self.db = mod_dbaccess.mod_dbaccess()
        
        except:
            self.db = None

    
    def name(self):
        """ Returns the name of this application.
        """
        return self.__class__.__name__
    
    
    def pushEvent(self, event):
        """ Pushes an event back to the main application.
        Pushes an event into the queue of the entity that instantiated this application. Such a queue is not required to operate the main app.
        """
        if self.outqueue != None:
            try:
                self.outqueue.put(event)
            
            except:
                pass
    

    def run(self):
        """ Main Thread function.
        """
        self.runAlways = True
        #print ("Start : %s" % time.ctime() )
        while self.runAlways:
            event = None
            #! Check and handle incoming events.
            if self.inqueue != None:
                while not self.inqueue.empty():
                    event = self.inqueue.get()
                    if "event" in event:
                        
                        if event["event"]["type"] == "userevent":
                            #! I quit myself
                            if event["event"]["value"] == "quit":
                                self.stop()
                
                    #! A task in the queue perform is handled by simply calling its equal function name
                    elif "task" in event:
                        if event["task"]["to"] == self.name():
                            runfunc = "self." + event["task"]["value"] + "(options="+str(event["task"]["options"])+")"

                            try:
                                eval(runfunc)
                    
                            except:
                                pass
        
            try:
                self.runCB(event)
            
            except:
                pass
            
            time.sleep(self.sleeptime/1000)
        #print ( "Stop : %s" % time.ctime() )
    

    def stop(self):
        """ Stops the main thread function from running always.
        """
        self.runAlways = False
        print("Application "+self.name()+" stopped...")
        try:
            self.stopApplication()
        except:
            pass


