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
        super().__init__(self.worker, outqueue)
    
    def worker(self, event=None):
        print("in worker")
        print(event)
        if self.inqueue.empty():
            self.runAlways = False


if __name__ == "__main__":
    app=app_eventhandler()
    app.pushEvent(['hi'])
    app.run()
