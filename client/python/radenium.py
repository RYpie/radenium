#!/usr/bin/env python
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


from os import walk
import sys
import importlib
import queue
import time


#! ***************************************************************************************
#! Bringing all apps and paths into this application
#! ***************************************************************************************


addonDirs = []
apps = []


'''
for (dirpath, dirnames, filenames) in walk('addons'):
    addonDirs.extend(dirnames)
    break #! Walk one level...


#! Adding all paths before updating.
for dir in addonDirs:
    sys.path.append('addons/' + str(dir) )


for dir in addonDirs:
    _mount = str(dir).split("_")[0]
    if _mount == 'app':
        module = importlib.import_module( str(dir) )
        _class = getattr( module, str(dir) )
        apps.append( _class() )

    elif _mount == 'mod':
        pass

'''


class Radenium:
    def __init__(self):
        self.addonDirs = []
        self.apps = []
        self.outqueues = queue.Queue() #! Queue for all apps to announce their messages
        self.pushThroughQueues = []
        self.runAlways=False
        
        for (dirpath, dirnames, filenames) in walk('addons'):
            self.addonDirs.extend(dirnames)
            break #! Walk one level...

        #! Adding all paths before updating.
        for dir in self.addonDirs:
            sys.path.append('addons/' + str(dir) )

        que_count=0
        for dir in self.addonDirs:
            _mount = str(dir).split("_")[0]
            if _mount == 'app':
                module = importlib.import_module( str(dir) )
                _class = getattr( module, str(dir) )
                self.apps.append( _class(outqueue=self.outqueues) )
                
                que_count += 1
            
            elif _mount == 'mod':
                pass

        print(self.apps)
        print(self.outqueues)
        

    def worker(self):
        self.runAlways=True
        for app in self.apps:
            try:
                app.start()
            
            except Exception as e:
                print("Could not start application: ", e)
    
        while self.runAlways:
            if not self.outqueues.empty():
                thing=self.outqueues.get()
                if "queue" in thing:
                    if thing["queue"]["dir"]=="in":
                        self.pushThroughQueues.append(thing["queue"]["ref"])
            
                else:
                    for qpush in self.pushThroughQueues:
                        qpush.put(thing)
                    
                    self.handleEvent(thing)
        
            time.sleep(0.2)
            
    def handleEvent(self,event):
        if "event" in event:
            if event["event"]["type"] == "userevent":
                if event["event"]["value"] == "quit":
                    self.runAlways=False


if __name__ == "__main__":
    #! Which it always is:
    print ("Running Radenium!")
    radenium = Radenium()
    radenium.worker()
    
