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


class m3u8:
    def parse_m3u8( self, m3u8_text ):
        m3u8 = {}
        old_stream = False
        m3u8[ 'files' ] = []
        m3u8[ 'closed' ] = False
        m3u8_lines = m3u8_text.split( '\n' )
        lineCounter = 0
        file = {}
        for line in m3u8_lines:
            if '#EXT-X-ENDLIST' in line:
                #! Then this file is closed, because its an old file.
                m3u8['closed'] = True

            if '#EXTINF:' in line:
                file[ 'extinf' ] = line
                file[ 'name' ] = m3u8_lines[ lineCounter+1 ]
                m3u8[ 'files' ].append( file.copy() )
                file = {}

            lineCounter += 1

        return m3u8
        
    def putm3u8file( self, file_location, contents ):
        """! Actually this is a generic solution to save to a file. """
        f = open( file_location,'w' )
        m3u8_text = f.write( contents )
        f.close()
    
    def getm3u8file( self, file_location ):
        """! Actually this is a generic solution to read from a file. """
        f = open( file_location,'r' )
        m3u8_text = f.read()
        f.close()
        
        return m3u8_text
        
    def closem3u8file( self, file_location ):
        m3u8 = self.getm3u8file( file_location )
        m3u8_c = m3u8.split( '\n' )
        alreadyClosed = False
        for line in m3u8_c:
            if '#EXT-X-ENDLIST' in line:
                alreadyClosed = True
                
        if not alreadyClosed:
            m3u8 += '\n#EXT-X-ENDLIST\n'
            self.putm3u8file(file_location, m3u8)
        
        print m3u8


if __name__ == "__main__":
    print "\nNot really something here to demonstrate...\n"
        
        
