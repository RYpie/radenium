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
import sys
import threading
import subprocess
from subprocess import Popen, PIPE, STDOUT, call
import os
from threading import Thread
import platform
try:
    from Queue import Queue, Empty
except ImportError:
    from queue import Queue, Empty


__MAC_OS__ = 'Darwin'
__WINDOWS__ = 'win32'
__LINUX__ = 'linux'


class ffmpeg_info:
    """! Class to obtain video & audio devices independent of system. """
    
    def __init__(self):
        self.log( "Starting..." )
    
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

    def getSystem(self):
        """! Return the system name of the operating platform. """
        thisSystem = platform.system()
        self.log("System: " + str( thisSystem ) )
        return thisSystem
    
    def getSystemDevices(self):
        """! getSystemDevices() returns a dictionary containing all system devices, for example: {"audio": [["0", "Built-in Microphone"], ["1", "Aggregate Device"]], "video": [["0", "FaceTime HD Camera"], ["1", "Capture screen 0"]]} """
        videodevices = []
        audiodevices = []
        if platform.system() == __LINUX__:
            #! For this to work on Linux requires to install video for Linux utilities.
            #! installation of video for Linux utilities: sudo apt install v4l-utils
            #! list camera devices: #list camera devices: v4l2-ctl --list-devices
            #! list usb video capabilities: v4l2-ctl --list-formats-ext
            #! or use: ffmpeg -f v4l2 -list_formats all -i /dev/video0
            
            #! Get a list of all video devices:
            command = ["v4l2-ctl", "-list-devices"]
            p = subprocess.Popen( command, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            out = p.communicate()
            
            #! Parsing the terminal output:
            if not "Failed" in out[1]:
                devString = "Video input set to "
                for line in out[0].split('\n'):
                    #print line
                    if devString in line:
                        _dev = ( line.split(devString)[1] ).split(" ")[0]
                        #print "Device = " + str(_dev)
                        videodevices.append(["/dev/video"+str(_dev), "Linux Camera Device " + str(_dev) ])
        
            else:
                self.log("Video utils not installed, use: sudo apt install v4l-utils", level="error" )
        
            #! Screen grabber is not listed, therefore append it now.
            videodevices.append(["x11grab", "Linux Capture screen"])
            
            #! Get a list of all audio devices:
            command = ['arecord', '-l']
            p = subprocess.Popen( command, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            out = p.communicate()
            
            #! Parsing the terminal output:
            for line in out[0].split('\n'):
                print line
                if 'card' in line:
                    #! for example: card 2: U0x46d0x825 [USB Device 0x46d:0x825], device 0: USB Audio [USB Audio]
                    #! Then get me the 2 indicating which card + a comma like ',' and the device number on the specific card. A card can have multiple devices.
                    _alsa = "hw:" + ((line.split(',')[0]).split(':')[0]).split(' ')[1] + "," + ((line.split(',')[1]).split(':')[0]).split('device ')[1]
                    audiodevices.append( [ _alsa, "Linux "+ str( ((line.split(',')[1]).split(':')[1])[1:]) ] )


        elif platform.system() == __MAC_OS__:
            #! Command: ffmpeg -f avfoundation -list_devices true -i ""
            ffmpegCommand = ["ffmpeg", "-f", "avfoundation", "-list_devices", "true", "-i", ""]
            p = subprocess.Popen( ffmpegCommand, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            out = p.communicate()

            #! Parsing terminal output
            startVideoList = False
            startAudioList = False
            
            for line in out[1].split('\n'):
                if "AVFoundation input device" in line:
                    if "AVFoundation video devices:" in line:
                        startVideoList = True
                        startAudioList = False
                    elif "AVFoundation audio devices:" in line:
                        startVideoList = False
                        startAudioList = True
                    
                    if startVideoList:
                        if not "AVFoundation video devices:" in line:
                            videodevices.append( (line.split('] [')[1]).split('] ') )
                    elif startAudioList:
                        if not "AVFoundation audio devices:" in line:
                            audiodevices.append( (line.split('] [')[1]).split('] ') )
                    else:
                        startVideoList = False
                        startAudioList = False

        elif platform.system() == __WINDOWS__:
            #! \todo implement the windows version.
            self.log( "Oops, our bad... Detection of devices on Windows not yet implemented... ", level="error" )
            pass

        else:
            pass

        sysDevs = {"video":videodevices, "audio":audiodevices}
        self.log( "Devices: " + str( sysDevs ) )
        return sysDevs



if __name__ == "__main__":
    logging.basicConfig(filename='mod_ffmpegwrapper.log',level=logging.DEBUG)
    info = ffmpeg_info()
    print info.getSystem()
    print info.getSystemDevices()
    
