#!/usr/bin/env python3
# -*- coding: utf-8 -*-


import sys


try:
    sys.path.append('../mod_app')
    import mod_app
except:
    pass


class app_anapp(mod_app.mod_app):
    """ Example class that implements the mod_app class usage
    Mod_app is the default threaded application class used by radenium.py to integrate all applications.
    """
    def __init__(self, outqueue=None):
        """ Constructor
            An queue can be applied to which the application will send matter that is interesting for outer space.
        """
        #! Registering app worker, called in run thread and the queue for anything interesting to be send to outer space.
        super().__init__(self.worker, outqueue)
    
    
    def worker(self, event=None):
        """ Called by super().run() function
        This function gets called periodically by the run function of the mod_app class.
        """
        pass


    def stopApplication(self):
        """ Called by super().stop() function
        """
        pass


if __name__ == "__main__":
    app=app_eventhandler()
    app.pushEvent(['this_is_an_event'])
    app.run()
