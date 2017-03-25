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
if __name__ == "__main__":
    sys.path.append('../mod_ffmpegwrapper')

#! Local imports
import mod_ffmpegwrapper as ffmpeg_wrapper


__DEVICE_OCCUPIED_KEY__ = "occupied"
__DEVICE_AVAIBLE_KEY__  = "available"

class mod_encoderdeck:
    """
    Intantiates ffmpeg processes.
    """
    def __init__(self):
        self.log( "Starting..." )
        self.system = ""
        self.video_devices = []
        self.audio_devices = []
        self.video_devices_status = {}
        self.audio_devices_status = {}
        self.encoderPipes = []
        self.refreshDevices()
    
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

    def refreshDevices( self ):
        """
        Finds system devices and registers them into a status register. By default are newly found devices available, because it is assumed that this application occupies and frees system devices.
        """
        
        ffmpeg = ffmpeg_wrapper.ffmpeg_info()
        self.system = ffmpeg.getSystem()
        system_devices = ffmpeg.getSystemDevices()
        #! \todo And now check for other devices as well.
    
        for dev in system_devices[ 'video' ]:
            dev_key = str( dev[0] ) + "::" + str( dev[1] )
            self.video_devices.append( dev_key )
            if not dev_key in self.video_devices_status:
                self.video_devices_status[ dev_key ] = { "status":__DEVICE_AVAIBLE_KEY__, "sys_id":dev[0] }
        
        for dev in system_devices[ 'audio' ]:
            dev_key = str( dev[0] ) + "::" + str( dev[1] )
            self.audio_devices.append( dev_key )
            if not dev_key in self.audio_devices_status:
                self.audio_devices_status[ dev_key ] = { "status":__DEVICE_AVAIBLE_KEY__, "sys_id":dev[0] }

    def getSystem( self ):
        return self.system
    
    def getDevicesStatus( self ):
        return { 'video':self.video_devices_status, 'audio':self.audio_devices_status }
    
    def getDevices( self ):
        return { 'video':self.video_devices,'audio':self.audio_devices }

    def encoderStart( self, method, inputVideoDevice=None, inputAudioDevice=None, inputFile=None, options={} ):
        """ 
        Starts encoding an input device or a combination of input devices, if possible, into an output file by a particular method.
        @param method abstract string representing a ffmpeg process. For example 'HLS' represents a whole arsenal of options to encode to HLS
        """


        print "Starting encoding... target directory should be in config file read by ffmpeg_wrapper"


        Vdev = None
        Adev = None
        URLdev = None

        #! Check if the system devices are available, meaning, they are not already used in an encoderpipe
        if inputVideoDevice != None:
            if self.video_devices_status[inputVideoDevice]['status'] != __DEVICE_OCCUPIED_KEY__:
                self.video_devices_status[inputVideoDevice]['status'] = __DEVICE_OCCUPIED_KEY__
                Vdev = self.video_devices_status[inputVideoDevice]['sys_id']
    
        if inputAudioDevice != None:
            if self.audio_devices_status[inputAudioDevice]['status'] != __DEVICE_OCCUPIED_KEY__:
                self.audio_devices_status[inputAudioDevice]['status'] = __DEVICE_OCCUPIED_KEY__
                Adev = self.audio_devices_status[inputAudioDevice]['sys_id']

        """def __init__(self,
             streamID,
             sys_video      = __DEFAULT_SYSTEM_VIDEO_DEVICE__,
             sys_audio      = __DEFAULT_SYSTEM_AUDIO_DEVICE__,
             url            = None,
             resolution     = __DEFAULT_BROADCAST_RESOLUTION__,
             cb_Rdy         = None,
             altSettings    = {} ):"""
        
        
        #! Check by which method encode.
        if method == "HLS":
            streamid = str( len( self.encoderPipes ) )
            self.encoderPipes.append(
                                        ffmpeg_wrapper.FFmpegStreamProcess( "streamid"+ streamid,
                                        sys_video=Vdev,
                                        sys_audio=Adev)
                                    )

    def encodeStop( self ):
        print "Stopping encoding..."

    def encoderDeckShutdown( self ):
        pipeid = 0
        for pipe in self.encoderPipes:
            pipe.stopStream()
            del self.encoderPipes[ pipeid ]
            pipeid += 1


if __name__ == "__main__":
    logging.basicConfig( filename = 'mod_encoderdeck.log', level = logging.DEBUG)
    enc = mod_encoderdeck()
    print "\nRunning Encoder deck on " + str( enc.getSystem() )
    print "\n"
    print "\nSystem devices:"
    print enc.getDevices()
    print "\n"
    print "\nAvailability:"
    print enc.getDevicesStatus()
    print "\n"
    print enc.encoderStart('HLS',inputVideoDevice='0::FaceTime HD Camera',inputAudioDevice='0::Built-in Microphone')
    print enc.encoderPipes
    enc.encoderDeckShutdown()
    print enc.encoderPipes



