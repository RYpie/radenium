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


_DBTBL_ENCODERDECK_MEDIADEVICES = "radenium_encdck_mediadevices"
_DBTBL_ENCODERDECK_ENCODETASKS = "radenium_encdck_encodetasks"


_DB_TABLES={}
_DB_TABLES[_DBTBL_ENCODERDECK_MEDIADEVICES] = (
                    "CREATE TABLE `"+_DBTBL_ENCODERDECK_MEDIADEVICES+"` ("
                    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
                    "  `name` varchar(50) NOT NULL,"
                    "  `type` varchar(50) NOT NULL,"
                    "  `idstr` varchar(50) NOT NULL,"
                    "  `sys_id` varchar(50) NOT NULL,"
                    "  PRIMARY KEY (`id`)"
                    ") ENGINE=InnoDB")


class app_encoderdeck(mod_app.mod_app):
    '''
    Class that operates the encoder muxer. Encoding is started by providing the id string of a device from the muxer available devices. An id has to be provided which will be the directory where the encoded video will be located. Furthermore has the target type to be provided which is the type of video you want made.
    '''
    def __init__(self, outqueue=None):
        super().__init__(self.worker, outqueue, sleeptime=400)
        self.muxer=mod_mediadevices.mod_mediamuxer()

        self.initdb()
    
    
    def initdb(self):
        try:
            dbdevs=self.db.select(_DBTBL_ENCODERDECK_MEDIADEVICES, "*")
        
        except Exception as e:
            #! Jajaja this is dirty but you are more dirty...
            if "1146" in str(e):
                self.db.createtable(_DB_TABLES)


    def getdbdevices(self):
        return self.db.select(_DBTBL_ENCODERDECK_MEDIADEVICES, "*")


    def db_savedevices(self,options):
        """ Writes available devices into the database
        """
        #! Fetch the data currently in the database, perhaps the system changed configuration.
        dbdevices=self.getdbdevices()   #! List of tuples
        muxdevices=self.muxer.devices() #! List of dictionary with keys.
        deletedevs=[]
        adddevs=[]

        #! Registering the devices I require to add
        for d in muxdevices:
            deviceknown=False
            for dbd in dbdevices:
                if d["idstr"] == dbd[3] and d["sys_id"] == dbd[4]:
                    deviceknown=True
                    break #! Get out of the loop
        
            if not deviceknown:
                adddevs.append(d)
    
        #! Next devices to be to deleted
        for dbd in dbdevices:
            deviceknown=False
            for d in self.muxer.devices():
                if str(d["idstr"]) == str(dbd[3]) and str(d["sys_id"]) == str(dbd[4]):
                    deviceknown=True
                    break #! Get out of the loop
        
            if not deviceknown:
                #! Append the id of the database device
                deletedevs.append(dbd[0])

        for d in adddevs:
            cols=["name","type","idstr","sys_id"]
            vals=[d["name"],d["type"],d["idstr"],d["sys_id"]]
            self.db.insert(_DBTBL_ENCODERDECK_MEDIADEVICES, cols, vals)

        for dbd in deletedevs:
            try:
                self.db.delete(_DBTBL_ENCODERDECK_MEDIADEVICES, where="id="+str(dbd))
                pass
            
            except Exception as e:
                print (e)


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
