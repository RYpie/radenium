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


import os


class mod_filesystem:
    def __init__(self):
        self.drives = []
        self.scansystem()

    def scansystem(self):
        """ Scans which drives are available and whether there is radenium media on it.
        """
        try:
            import psutil
            self.drives = psutil.disk_partitions()

        except:
            self.drives = os.listdir('/Volumes')

        print self.drives

        for drive in self.drives:
            if os.path.exists("/Volumes/" + str(drive) ):
                print "Found one on: " + str(drive)
            
            print os.listdir("/Volumes/" + str(drive))




if __name__ == "__main__":
    pass
    fs = mod_filesystem()

