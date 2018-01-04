#!/usr/bin/env python3
# -*- coding: utf-8 -*-


import sys
import queue


#! Local imports
if __name__ == "__main__":
    sys.path.append('../mod_app')
    sys.path.append('../mod_mediadevices')
    sys.path.append('../mod_ffmpegwrapper')


try:
    import mod_app
except:
    pass

try:
    import mod_mediadevices
except:
    pass

try:
    import mod_ffmpegwrapper
except:
    pass


class app_encoderdeck(mod_app.mod_app):
    '''
    Class that operates the encoder muxer. Encoding is started by providing the id string of a device from the muxer available devices. An id has to be provided which will be the directory where the encoded video will be located. Furthermore has the target type to be provided which is the type of video you want made.
    '''
    def __init__(self, outqueue=None):
        super().__init__(self.worker, outqueue, sleeptime=400)
        self.muxer=mod_mediadevices.mod_mediamuxer()
    
    
    def task_test_encode_syscam(self, options):
        vid = '0_facetime_hd_camera'
        aid = '0_built-in_microphone'
        progid="testlive/episode_0"
        self.muxer.setrecordmux(progid,"apple_hls",vid, aid)
        self.muxer.startencode(progid)
    
    
    def task_encode_start(self, options):
        self.muxer.setrecordmux(
                options["prog_id_str"]
                , options["format"]
                , options["vid"]
                , options["aid"]
            )
        self.muxer.startencode(options["prog_id_str"])

    
    def task_encode_stop(self, options):
        self.muxer.stopencode(options["prog_id_str"])
        
    
    def worker(self, event=None):
        #print("in worker of encoder deck")
        pass
    
    
    def stopApplication(self):
        self.muxer.shutdown()

if __name__ == "__main__":
    pass
    #app=app_encoderdeck()
    #app.pushEvent(['hi'])
    #app.run()
