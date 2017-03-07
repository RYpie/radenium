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


class mod_systemdevices:
    def __init__(self):
        self.system = ""
        self.devices = {}
        self.getSystemDevices()
        self.log("Starting...")
    
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

    def getSystemInfo(self):
        ffmpeg = ffmpeg_wrapper.ffmpeg_info()
        self.system = ffmpeg.getSystem()
        self.devices = ffmpeg.getSystemDevices()
        #! \todo And now check for other devices as well.
    
    def run(self):
        pass

if __name__ == "__main__":
    logging.basicConfig( filename = 'mod_systemdevices.log', level = logging.DEBUG)
    print "Nothing to debug, so a useless addon..."


