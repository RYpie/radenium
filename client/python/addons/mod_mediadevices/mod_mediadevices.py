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


import logging
import datetime
import threading
import os
import sys
import time

#! Local imports
if __name__ == "__main__":
    sys.path.append('../mod_ffmpegwrapper')
    sys.path.append('../mod_wsdiscovery')

import mod_ffmpegwrapper as ffmpeg_wrapper

#! Try importing wsdiscovery for python3 otherwise a modified version by myself.
try:
    import WSDiscovery, QName, Scope

except:
    from mod_wsdiscovery_01 import WSDiscovery


class mod_mediadevices:
    def __init__(self):
        self.log("Starting...")
        self.system = ""
        self.devices = []
        self._get_FfmpegSystemInfo()
        self.wsdiscovery()
    
    
    def log( self, text, level="info" ):
        log_text = str( datetime.datetime.now() )
        log_text += " "
        log_text += str( self.__class__.__name__ )
        log_text += " "
        log_text += text
        if level == "warning":
            logging.warning( log_text )
        
        elif level == "error":
            logging.error( log_text )
        
        else:
            logging.info( log_text )


    def _get_FfmpegSystemInfo(self):
        ffmpeg = ffmpeg_wrapper.ffmpeg_info()
        self.system = ffmpeg.getSystem()
        devices = ffmpeg.getSystemDevices()
        for dev in devices['video']:
            ids = str(dev[0])+"_"+str(dev[1]).lower().replace(" ","_")
            self.devices.append({"sys_id":dev[0], "name":dev[1], "type":"video", "id":ids})
        
        for dev in devices['audio']:
            ids = str(dev[0])+"_"+str(dev[1]).lower().replace(" ","_")
            self.devices.append({"sys_id":dev[0], "name":dev[1], "type":"audio", "id":ids})


    def wsdiscovery(self):
        wsd = WSDiscovery.WSDiscovery()
        wsd.start()
        '''
        ttype = QName("abc", "def")

        ttype1 = QName("namespace", "myTestService")
        scope1 = Scope("http://myscope")
        ttype2 = QName("namespace", "myOtherTestService_type1")
        scope2 = Scope("http://other_scope")

        xAddr = "localhost:8080/abc"
        wsd.publishService(types=[ttype], scopes=[scope2], xAddrs=[xAddr])
        '''
        #ret = wsd.searchServices(scopes=[scope1], timeout=10)
        ret = wsd.searchServices()

        for service in ret:
            print(service.getEPR() + ":" + service.getXAddrs()[0])

        wsd.stop()


    def mediaDevices(self):
        
        return {"system":self.system, "devices":self.devices}
    
    
    def run(self):
        pass

if __name__ == "__main__":
    logging.basicConfig( filename = 'mod_mediadevices.log', level = logging.DEBUG)
    devices = mod_mediadevices()
    print (devices.mediaDevices())
    

