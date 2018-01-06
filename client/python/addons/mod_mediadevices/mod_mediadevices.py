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
import shutil


#! Local imports
if __name__ == "__main__":
    sys.path.append('../mod_ffmpegwrapper')
    sys.path.append('../mod_wsdiscovery')
    sys.path.append('../mod_dbaccess')


import mod_ffmpegwrapper
import mod_dbaccess


#! Try importing wsdiscovery for python3 otherwise a modified version by myself.
try:
    import WSDiscovery

except Exception as e:
    print (e)
    from mod_wsdiscovery_01 import WSDiscovery


class mod_mediamuxer:
    def __init__(self):
        self.encprocesses = []
        self.mediadevices = mod_mediadevices()
        self.encprocessids=0
        self.livemuxtable = {}
    
    
    def devices(self):
        return self.mediadevices.mediaDevices()
    
    
    def refreshDevices(self):
        return self.mediadevices
    
    
    def getmuxtable(self):
        return self.livemuxtable
    
    
    def setrecordmux(self, streamId, format,vid=None, aid=None, file=None):
        vid_id=None
        aid_id=None
        for dev in self.devices():
            if dev["type"] == "video":
                if dev["idstr"] == vid:
                    vid_id=dev["sys_id"]
        
            if dev["type"] == "audio":
                if dev["idstr"] == aid:
                    aid_id=dev["sys_id"]
        
        if vid_id != None and aid_id != None:
            if not str(streamId) in self.livemuxtable:
                self.livemuxtable[str(streamId)] = {
                    "format":format
                    , "vid":vid_id
                    , "aid":aid_id
                    , "file":None
                    , "process":None
                    , "streamlocation":str(streamId)
                }


        #! Every time I start a stream I should check if the input devices are not already occupied to a process.
        #!


    def startencode(self, streamId):
        print("launching", streamId)
        for key, value in self.livemuxtable.items():
            print(key)
            if str(key) == str(streamId):
                print("starting ", streamId)
                try:
                    encode_proc = mod_ffmpegwrapper.FFmpegStreamProcess(self.livemuxtable[key])
                    encode_proc.run()

                except Exception as e:
                    print(e)
                
                self.livemuxtable[key]["process"]=encode_proc
                dt = str( datetime.datetime.now() )
                self.livemuxtable[key]["tstart"]=dt

    
    def stopencode(self, streamId):
        for key, value in self.livemuxtable.items():
            if str(key) == str(streamId):
                print("Stopping ", streamId)
                self.livemuxtable[key]["process"].stopStream()
                #! Creating a subdirectory for storing the stream
                infilelocation=self.livemuxtable[key]["process"].filename()
                dt = str( self.livemuxtable[key]["tstart"] )
                dt = dt.replace('-','_')
                dt = dt.replace(' ','/')
                dt = dt.replace(':','_')
                dt = dt.replace('.','/')
                #! Subdirectory format = /[start date]/[start time]/[fraction of seconds]
                outfilelocation=infilelocation.replace("out/", dt)
                shutil.move( infilelocation, outfilelocation )


    def shutdown(self):
        for key, value in self.livemuxtable.items():
            try:
                #! Stop all streams and store the results into another directory.
                self.stopencode(key)
                #self.livemuxtable[key]["process"].stopStream()
            
            except:
                pass


class mod_mediadevices:
    '''
    Muxer class for all media devices, keeps also track of which devices currently are used.
    Should be renamed to media muxer.
    '''
    def __init__(self):
        self.log("Starting...")
        self.system = ""
        self.devices = [] #! All camera devices
        self.formats = [] #! All output formats
        self.refreshDevices()
    
    
    def refreshDevices(self):
        self._get_FfmpegDevices()
        self._get_FfmpegFormats()
        #self.wsdiscovery()
    
    
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


    def _get_FfmpegFormats(self):
        ffmpegCommands=mod_ffmpegwrapper.FFmpegCommand()
        #print(ffmpegCommands.appleHLS(0,0,'abc') )


    def _deviceDict(self, type, sysid, name, available):
        ids = str(sysid)+"_"+str(name).lower().replace(" ","_")
        return {"sys_id":sysid, "name":name, "type":type, "idstr":ids, "available":available}


    def _get_FfmpegDevices(self):
        ffmpeg = mod_ffmpegwrapper.ffmpeg_info()
        self.system = ffmpeg.getSystem()
        devices = ffmpeg.getSystemDevices()
        #print(devices)
        for dev in devices['video']:
            self.devices.append(self._deviceDict("video", dev[0], dev[1], True))
        
        for dev in devices['audio']:
            self.devices.append(self._deviceDict("audio", dev[0], dev[1], True))


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
            #!todo test onvif camera's
            #self.devices.append(self._deviceDict("onvif", None, None, True))
            
        wsd.stop()


    def systemPlatform(self):
        return self.system


    def mediaDevices(self):
        return self.devices
    
    
    def run(self):
        pass


if __name__ == "__main__":
    pass
    #logging.basicConfig( filename = 'mod_mediadevices.log', level = logging.DEBUG)
    #devices = mod_mediadevices()
    #print (devices.mediaDevices())
