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


#! For the keyboard hit etc.
import glob
import sys, termios, atexit
from select import select


import queue

sys.path.append('../mod_app')
try:
    import mod_app
except:
    pass


__USEREVENTS = {
    "quit":{"event":{"type":"userevent", "value":"quit"}}
}
__TASKS = {
    "app_encoderdeck.encode":{"task":{"to":"app_encoderdeck", "value":"encode", "options":{"vid":0,"aid":0}}}
    , "app_encoderdeck.stopencode":{"task":{"to":"app_encoderdeck", "value":"stopencode", "options":{"vid":0,"aid":0}}}
}


class app_eventhandler(mod_app.mod_app):
    '''
        Class to handle events.
    '''
    def __init__(self, outqueue=None):
        super().__init__(self.worker, outqueue, sleeptime=400)
    
    
    def worker(self, event=None):
        #print("in worker of eventhandler")
        #print(event)
        if self.kbhit():
            letter = self.getch()
            if letter == 'q':
                self.pushEvent({"event":{"type":"userevent", "value":"quit"}})
    
            elif letter == 'g':
                self.pushEvent({"task":{"to":"app_encoderdeck", "value":"task_test_encode_syscam", "options":{}}})
            
            elif letter == 's':
                self.pushEvent({"task":{"to":"app_encoderdeck", "value":"task_encode_stop", "options":{"prog_id_str":"leuk/joh"}}})

            elif letter == 'e':
                self.pushEvent({"task":{"to":"app_encoderdeck", "value":"task_encode_start", "options":{
                    "vid":'0_facetime_hd_camera'
                    , "aid":'0_built-in_microphone'
                    , "prog_id_str":"leuk/joh"
                    , "format":"apple_hls"
                }}})

            elif letter == 'd':
                #! Test to store devices to the database.
                self.pushEvent({"task":{"to":"app_encoderdeck", "value":"db_savedevices", "options":{}}})

    def kbhit(self):
        dr,dw,de = select([sys.stdin], [], [], 0)
        return dr != []


    def putch(self,ch):
        sys.stdout.write(ch)


    def getch(self):
        return sys.stdin.read(1)


    def getche(self):
        ch = self.getch()
        putch(ch)
        return ch


if __name__ == "__main__":
    #systemqueue = queue.Queue()
    
    app=app_eventhandler()
    app.pushEvent(['hi'])
    #app.run()
