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
import pprint

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
_DBTBL_TAKES = "radenium_takes"

_DB_TABLES={}
_DB_TABLES[_DBTBL_ENCODERDECK_MEDIADEVICES] = (
                                            "CREATE TABLE `"+_DBTBL_ENCODERDECK_MEDIADEVICES+"` ("
                                            "  `id` int(11) NOT NULL AUTO_INCREMENT,"
                                            "  `name` varchar(50) NOT NULL,"
                                            "  `type` varchar(50) NOT NULL,"
                                            "  `idstr` varchar(50) NOT NULL,"
                                            "  `sys_id` varchar(50) NOT NULL,"
                                            "  `user_id` int(11) NOT NULL,"
                                            "  PRIMARY KEY (`id`)"
                                            ") ENGINE=InnoDB")


_DB_TABLES[_DBTBL_ENCODERDECK_ENCODETASKS] = (
                                               "CREATE TABLE `"+_DBTBL_ENCODERDECK_ENCODETASKS+"` ("
                                               "  `id` int(11) NOT NULL AUTO_INCREMENT,"
                                               "  `publish` int(1) NOT NULL,"
                                               "  `state` int(1) NOT NULL,"
                                               "  `vid` varchar(50) NOT NULL,"
                                               "  `aid` varchar(50) NOT NULL,"
                                               "  `prog_id_str` varchar(50) NOT NULL,"
                                               "  `format` varchar(50) NOT NULL,"
                                               "  `user_id` int(11) NOT NULL,"
                                               "  `taskdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,"
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
        if self.db != None:
            try:
                #! Loop over all database table to see if one does not exists.
                for dbtable, ddl in _DB_TABLES.items():
                    dbcall=self.db.select(dbtable, "*", where="id=1")
        
            except Exception as e:
                print(e)
                #! Jajaja this is dirty but you are more dirty...
                if "1146" in str(e):
                    self.db.createtables(_DB_TABLES)


    def getdbdevices(self):
        dbresult=[]
        if self.db != None:
            dbresult = self.db.select(_DBTBL_ENCODERDECK_MEDIADEVICES, "*")
    
        return dbresult
    
    
    def get_encoderdecktasks(self):
        tasks=[]
        try:
            #! state 0 means freshly added, state 2 means, freshly stopped.
            # dbtasks=self.db.select_rec(_DBTBL_ENCODERDECK_ENCODETASKS, "*", where="state=0 OR state=2")
            dbtasks=self.db.select_rec(_DBTBL_TAKES, "*", where="state=0 OR state=2")
        
        except Exception as e:
            print(e)
        
        
        #return tasks
        for t in dbtasks:
            tasks.append(
                {
                    "dbid": t[0]
                    , "state":t[7]
                    , "publish":t[6]
                    #, "prog_id_str":t[5]
                    , "format":t[5]
                    , "vid":t[2]
                    , "aid":t[3]
                    , "taskdate":t[9]
                }
            )
        
        return tasks
    
    def set_encoderdecktaskstate(self, task, state):
        """ Changes the task that is running to a different state.
        """
        try:
            if state == 'started':
                if "dbid" in task:
                    self.db.update(_DBTBL_TAKES, ['state'], [1], task["dbid"])
        
            elif state== "stopped":
                if "dbid" in task:
                    self.db.update(_DBTBL_TAKES, ['state'], [3], task["dbid"])
    
        except Exception as e:
            print(e)

    def db_savedevices(self,options):
        """ Writes available devices into the database
        """
        if self.db != None:
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
        """ Sets first the multiplexer into the state to record.
            setrecordmux creates a new ffmpegthread, startencode, starts it.
        """
        recordId = "takes/" + "id_"+str(options["dbid"])
        self.muxer.setrecordmux(
                recordId
                , options["format"]
                , options["vid"]
                , options["aid"]
            )
            
        self.muxer.startencode(recordId)

    
    def task_encode_stop(self, options):
        recordId = "takes/" + "id_"+str(options["dbid"])
        
        self.muxer.stopencode(recordId)
        
    
    def worker(self, event=None):
        tasks=self.get_encoderdecktasks()
        print(tasks)
        if len(tasks) > 0:
            for t in tasks:
                print(t)
                if t["state"] == 0:
                    print("Starting task ", t)
                    self.task_encode_start(t)
                    self.set_encoderdecktaskstate(t, 'started')

                elif t["state"] == 2:
                    print("Stopping task ", t)
                    self.task_encode_stop(t)
                    self.set_encoderdecktaskstate(t, 'stopped')




    def stopApplication(self):
        self.muxer.shutdown()


if __name__ == "__main__":
    pass
    #app=app_encoderdeck()
    #app.pushEvent(['hi'])
    #app.run()
