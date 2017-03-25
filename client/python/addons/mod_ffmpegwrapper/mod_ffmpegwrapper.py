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


__MAC_OS__                                  = 'Darwin'
__WINDOWS__                                 = 'win32'
__LINUX__                                   = 'linux'
__DEFAULT_SYSTEM_VIDEO_DEVICE__             = 0
__DEFAULT_SYSTEM_AUDIO_DEVICE__             = 0
__DEFAULT_BROADCAST_RESOLUTION__            = "640X320"
__MEDIA_NETWORK_STREAMS_LOCATION__          = "../../media/"
__MEDIA_NETWORK_STREAMS_SEGMENT_DURATION__  = 10


class FFmpegStreamProcess(object):
    """
    Class to execute FFmpeg.
    """
    def __init__(self,
                 streamID,
                 sys_video      = __DEFAULT_SYSTEM_VIDEO_DEVICE__,
                 sys_audio      = __DEFAULT_SYSTEM_AUDIO_DEVICE__,
                 url            = None,
                 resolution     = __DEFAULT_BROADCAST_RESOLUTION__,
                 cb_Rdy         = None,
                 altSettings    = {} ):
        
        self.terminateMyself    = False
        self.sys_video          = sys_video    #! Input device dict from which the stream has to be received.
        self.sys_audio          = sys_audio
        self.url                = url
        self.streamID           = streamID #! The id of the directory where the stream is placed.
        self.streamLocation     = __MEDIA_NETWORK_STREAMS_LOCATION__ + self.streamID + "/"
        self.altSettings        = altSettings
        if not os.path.exists( self.streamLocation ):
            os.makedirs( self.streamLocation )
            print ">>> Created Stream location: " + str(self.streamLocation)
        
        segdur = str( __MEDIA_NETWORK_STREAMS_SEGMENT_DURATION__ )
        if (self.sys_audio != None) and (self.sys_video != None):
            #! Video and audio livestream
            if "LinuxMint" in platform.platform():
                print self.sys_audio
                print self.sys_video
                print "------------------------------"
                device = "/dev/video0"
                _fa = "alsa"
                _fac = "2"
                #_fac = "1" #should be the number behind the comma of _fai
                _fai = "hw:0,0" #op hw:2,0 issues in dit process, das de camera audio.
                _fai = self.sys_audio
                _fai = "hw:3,0" #Logitech Pro
                _far = "44100"
                _fv = "v4l2"
                _thread_queue_size = "1028"
                _frate = "10"
                _strict = "experimental"
                _strict2 = "-2"
                
                self.command = ["ffmpeg", "-f", _fv, "-timestamps", "abs", "-i", device, "-thread_queue_size", _thread_queue_size, "-f", _fa,"-ac",_fac,"-i",_fai, "-async", "100000", "-ar", _far, "-preset", "ultrafast", "-c:v", "libx264", "-tune", "zerolatency", "-pix_fmt", "yuv420p", "-profile:v", "baseline", "-level", "1.3", "-maxrate", "768K", "-bufsize", "1M", "-crf", "20", "-g", "20", "-f", "hls", "-hls_time", segdur,"-s",resolution, "-threads","0",  "-force_key_frames", "00:00:00.000", self.streamLocation + "playlist.m3u8"]
                
                #self.command = ["ffmpeg", "-f", _fv, "-timestamps", "abs", "-i", device, "-thread_queue_size", _thread_queue_size, "-f", _fa,"-ac",_fac,"-i",_fai, "-async", "100000", "-ar", _far, "-preset", "ultrafast", "-c:v", "copy", "-tune", "zerolatency", "-level", "1.3", "-maxrate", "576K", "-bufsize", "1M", "-crf", "20", "-g", "20", "-f", "hls", "-hls_time", segdur,"-s",resolution, "-threads","0",  "-force_key_frames", "00:00:00.000", self.streamLocation + "playlist.m3u8"]
                
                #! synching: bijvoorbeeld -threads 0 en -vsync 1   try changing audio and video, first audio dan video...
                print " ".join( self.command )
            
            else:
                #! its a mac!
                device = str(self.sys_video) + ":" + str(self.sys_audio)
                _fv = "avfoundation"
                _strict = ""
                _strict2 = ""
                _frate = "15"
                self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-pix_fmt", "yuv420p", "-s",resolution,"-hls_flags", "round_durations", "-hls_time","3","-hls_init_time","3", self.streamLocation + "playlist.m3u8"]
            
        elif (self.sys_audio != None) and (self.sys_video == None):
            #! Audio only
            device = ":" + str(self.sys_audio)
            #-codec:a libmp3lame -qscale:a 2 output.mp3
            #self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-codec:a","libmp3lame","-qscale:a", "2",self.streamLocation + "output.mp3"]
            self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-codec:a","libmp3lame","-qscale:a", "2",self.streamLocation + "playlist.m3u8"]
            #self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-c:a", "aac", "-strict", "experimental", "-ac", "2", "-b:a", "64k", "-ar", "44100", self.streamLocation + "playlist.m3u8"]
            
        elif (self.sys_audio == None) and (self.sys_video == None) and (self.url != None):
            #! Its a network streaming device
            #! Command: ffmpeg -re -i rtsp://username:password@ip_address:port/path -r 30 -f avfoundation "0" -map 0:v:0 -map 1:a:0 -pix_fmt yuv420p playlist.m3u8
            self.command = ["ffmpeg", "-re", "-i", self.url, "-c:v", "libx264", "-pix_fmt", "yuv420p", "-profile:v", "baseline", "-level", "1.3", "-maxrate", "192K", "-bufsize", "1M", "-crf", "18", "-r", "10", "-g", "30", "-f", "hls", "-hls_time", segdur,"-s",resolution, self.streamLocation + "playlist.m3u8"]
                
        else:
            device = str(self.sys_video)
            #self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-pix_fmt", "yuv420p", "-s",resolution, "-hls_flags", "round_durations", "-hls_time","3","-hls_init_time","3", self.streamLocation + "playlist.m3u8"]
            #self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-pix_fmt", "yuv420p", "-s",resolution, "-tune","zerolatency", self.streamLocation + "playlist.m3u8"]
            self.command = ["ffmpeg", "-r", "30", "-f", "avfoundation", "-i", device, "-c:v", "libx264", "-pix_fmt", "yuv420p", "-profile:v", "baseline", "-level", "1.3", "-maxrate", "192K", "-bufsize", "1M", "-crf", "18", "-r", "10", "-g", "30", "-f", "hls", "-hls_time", segdur, "-s",resolution, self.streamLocation + "playlist.m3u8"]
                
            self.queue = Queue()
            self.process = None
    
    
    def stopStream(self):
        try:
            self.terminateMyself = True
            
        except Exception, e:
            print self.__class__.__name__ + ".stopStream():"
            print "Could not stop stream... %s" % self.streamID
            print e
    
    
    def _queue_output(self, out, queue):
        '''
            while ( self.terminateMyself is False ):
            pass # this is to allow an ffmpeg command to be run externally which encoded result will become uploaded to the server.
            time.sleep(0.5)
            print "***** ATTENTION FFMPEG HAS TO BE STARTED EXTERNALLY FROM RADENIUM OR COMMENT THIS CODE!"
            '''

        """Read the output from the command bytewise. On every newline
            the line is put to the queue."""
        line = ''
        try:
            while (self.process.poll() is None ) and ( self.terminateMyself is False ):
                chunk = out.read(1).decode('utf-8')
                if chunk == '':
                    continue
                line += chunk
                if chunk in ('\n', '\r'):
                    queue.put(line)
                    if "ThreadLock" in line:
                        print "Audio Issue, threadlocks again..."
                        print line
                    
                    elif "error" in line:
                        print "FFMPEG ERROR!!!:"
                        print line
                        if "hw:" in line:
                            print "Try fiddling the -ac parameter and pick the right channel 1 or 2 or an other one. Make sure -ac is placed before the -i parameter. "
        
                    line = ''

            out.close()

        except Exception, e:
            print self.__class__.__name__ + ": Process was terminated..."
            print e
                
            try:
                self.process.terminate()
                self.process = None

            except Exception, e:
                print "ffmpeg was not started, because process did not exist" + str( e )
                print "Last ffmpeg line: " + str( line )
    
    
    def run(self, daemon=True):
        """
        Executes the command. A thread will be started to collect
        the outputs (stderr and stdout) from that command.
        The outputs will be written to the queue.
        
        :return: self
        """
        #self._queue_output({},{}) #hangs there if
        try:
            command = ["ls"]
            # if you want ffmpeg to workinside python again, set it back to self.command
            self.process = Popen(self.command, bufsize=5,
            stdin=PIPE, stdout=PIPE, stderr=STDOUT)
            thread = Thread(target=self._queue_output,
                         args=(self.process.stdout, self.queue))
            thread.deamon = daemon
            thread.start()
         
        except Exception, e:
            print"Could not launch stream: " + str( e )

        return self
    

    def readlines(self, keepends=False):
        """
        Yield lines from the queue that were collected from the
        command. You can specify if you want to keep newlines at the ends.
        Default is to drop them.
        
        :param keepends: keep the newlines at the end. Default=False
        """
        while self.process.poll() is None:
            try:
                line = self.queue.get(timeout=0.1)
                if keepends:
                    yield line
                else:
                    yield line.rstrip('\r\n')
        
            except Empty:
                pass
    
    
    def __getattr__(self, name):
        if self.process:
            return getattr(self.process, name)
        
        raise AttributeError
    
    
    def __iter__(self):
        return self.readlines()




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
    
