#!/usr/bin/env python3
# -*- coding: utf-8 -*-


import sys

sys.path.append('../mod_app')
try:
    import mod_app
except:
    pass




class app_anapp(mod_app.mod_app):
    def __init__(self, outqueue=None):
        #! Registering app worker, called in run thread and the queue for anything interesting to be send to outer space.
        super().__init__(self.worker, outqueue)
    
    def worker(self, event=None):
        pass

    def stopApplication(self):
        pass

if __name__ == "__main__":
    app=app_eventhandler()
    app.pushEvent(['this_is_an_event'])
    app.run()
