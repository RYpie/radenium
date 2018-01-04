#!/usr/bin/env python3
# -*- coding: utf-8 -*-


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
    '''
    base class for an app. extend it by giving it a worker function and an output queue to the world.
    '''
    def __init__(self, runCB=None, outqueue=None, sleeptime=1000):
        super().__init__()
        self.runAlways = False
        self.sleeptime = sleeptime
        self.outqueue = outqueue
        self.inqueue=queue.Queue()
        #! Announcing a reference to the inqueue of this class.
        self.pushEvent({"queue":{"dir":"in","ref":self.inqueue, "name":self.name()}})
        self.runCB = runCB
        self.db = mod_dbaccess.mod_dbaccess()
    
    def name(self):
        return self.__class__.__name__
    
    def pushEvent(self, event):
        if self.outqueue != None:
            try:
                self.outqueue.put(event)
            
            except:
                pass
    

    def run(self):
        self.runAlways = True
        #print ("Start : %s" % time.ctime() )
        while self.runAlways:
            event = None
            if self.inqueue != None:
                while not self.inqueue.empty():
                    event = self.inqueue.get()
                    if "event" in event:
                        
                        if event["event"]["type"] == "userevent":
                            #! I quit myself
                            if event["event"]["value"] == "quit":
                                self.stop()
                
                    #! Perform tasks in an app by simply calling its function
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
        self.runAlways = False
        print("Application "+self.name()+" stopped...")
        try:
            self.stopApplication()
        except:
            pass


