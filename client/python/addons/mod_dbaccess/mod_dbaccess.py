#!/usr/bin/env python


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


import mysql.connector
from mysql.connector import (connection)
from mysql.connector import errorcode

import json
import datetime
import re


_IOP_VIDEO_TABLE = "dcgbn_radenium_media_devices"
_IOP_VIDEO_RECORD = {
      "domain_id" : 8
    , "aid" : 8
    , "type" : 0
    , "description" : "bla"
    , "user_id" : 0
    
}


_PREFIX = "nt4pz"


class mod_dbaccess(object):
    def __init__( self, dbname="radenium" ):
        # Open database connection
        self.cnx = connection.MySQLConnection(user='root', password='root', host='localhost', port='8889', database=dbname)
    
    
    def createtable(self, tables):
        """ Creates a set of tables in the database.
        @param tables A dictionary with key name as table name and its value a tuple with the mysql create table query.
        """
        tname=""
        tddl=""
        cursor = self.cnx.cursor()
        try:
            for name, ddl in tables.items():
                if not _PREFIX in str(name):
                    tname = str(name).replace(str(name), _PREFIX+"_"+str(name))
                    tddl = str(ddl).replace(str(name), _PREFIX+"_"+str(name))
                
                try:
                    print("Creating table {}: ".format(tname), end='')
                    cursor.execute(tddl)
            
                except mysql.connector.Error as err:
                    if err.errno == errorcode.ER_TABLE_EXISTS_ERROR:
                        print("already exists.")
                    
                    else:
                        print(err.msg)
    
        except Exception as e:
            print(e)

        cursor.close()
    
    
    def delete(self, table, where=""):
        cursor = self.cnx.cursor()
        #! Verifying database prefix
        if not _PREFIX in table:
            query = ("DELETE FROM " + _PREFIX + "_" + str(table) )
        
        else:
            query = ("DELETE FROM " + str(table) )
        
        if where != "":
            query += " WHERE " + where
        print(query)
        cursor.execute(query)
        self.cnx.commit()
        cursor.close()
    
    
    def select(self, fromtable, select="*", where=""):
        cursor = self.cnx.cursor()
        query = ("SELECT " + select + " FROM " + _PREFIX + "_" + str(fromtable))
        if where != "":
            query += " WHERE " + where
            
        print("QUERY=", query)
        
        cursor.execute(query)
        result = cursor.fetchall()
        cursor.close()
        
        return result
        
        
    def update(self, totable, columns, values, id ):
        retval = {}
        cursor = self.cnx.cursor()
        cols = ", ".join(columns)
        vals = ""
        set_string = ""
        i = 0
        for v in values:
            '''
            if vals != "":
                vals +=", "
            vals += "%s"
            '''
            if i > 0:
                set_string += ", "
            set_string += str(columns[i]) + "=%s"
            i += 1
            
            
        add_domain = ("UPDATE " + str(totable) + " SET "
            + set_string
            + " WHERE id=" + str(id) )
               
        
        #print (add_domain, tuple(values))
        cursor.execute(add_domain, tuple(values))
        emp_no = cursor.lastrowid
        #print ( cursor.statement )
        #print("Saved into database no: %d"%(emp_no) )
        self.cnx.commit()
        #print ( cursor.statement )
        retval["query"] = cursor.statement
        cursor.close()
        retval['id'] = emp_no
        
        return retval
    
    
    def insert(self, totable, columns, values):
        retval = {}
        cursor = self.cnx.cursor()
        cols = ", ".join(columns)
        vals = ""
        for v in values:
            if vals != "":
                vals +=", "
            vals += "%s"
            
            
        add_domain = ("INSERT INTO " + _PREFIX + "_" + str(totable) + " "
               "(" + cols + ") "
               "VALUES (" + vals + ")")
               
        
        #print (add_domain, tuple(values))
        cursor.execute(add_domain, tuple(values))
        emp_no = cursor.lastrowid
        #print ( cursor.statement )
        #print("Saved into database no: %d"%(emp_no) )
        self.cnx.commit()
        #print ( cursor.statement )
        retval["query"] = cursor.statement
        cursor.close()
        retval['id'] = emp_no
        
        return retval
    
    def close(self):
        self.cnx.close()
        
        
        

class dbApi(object):
    def __init__( self ):  
        try:
            self.db = dbaccess.dBAccess()

        except:
            pass
            

    def saveSearchResults( self, domain_info, domain_index=None, domain_spy=None ):
        url_parsed = urlparse( domain_info["url"] )
        #print(domain_index)
        
        #print("I automatically save other domains here, should not happen!!!!!!!!!!!!!!")
        is_domain_id = {}
        if url_parsed.path == "" or url_parsed.path == "/":
            #! Only update if we just crawled the homepage
            is_domain_id = self.is_addDomain(domain_info,language=domain_index["language"],menu=json.dumps(domain_index["topics"]) )
            
        else:
            #! Otherwise store these pages somewhere else
            id = self.db.select("dcgbn_isearch_domains", select="id", where="host_url='"+url_parsed.netloc+"'" )
            if len(id) == 1:
                is_domain_id['id'] = str(id[0][0])
            else:
                is_domain_id['id'] = None
                
                
        if is_domain_id['id'] != None:
            #! The domain for this url exists in the domain table. Check if it has videos in it.
            try:
                #! Checking if videos have been found.
                #! Videos might be in iframes:
                if "videos" in domain_index:
                    if len(domain_index["videos"]) > 0:
                        print("Save or updating ",len(domain_index["videos"]),"videos\n")
                        for v in domain_index["videos"]:
                            self.is_addVideo(is_domain_id['id'], v)
                            
            except Exception as e:
                print( "IOP Error in class: " + self.__class__.__name__+ " while adding iframe videos to the database: " + str( e ))
                #print( domain_index["videos"] )
                
            #! Save social and feed data into
                
    def is_addVideo( self, is_domain_id, video ):
        result = False
        
        exists = self.db.select("dcgbn_isearch_videos", select="id", where="`url`='"+str(video['src'])+"'" )
        print("video url:",video['src']," id ", exists)
        
        if len(exists) == 0:
            #! It does not exist, so let's store it.
            print("Storing video to domain id: "+ str(is_domain_id) + " title " + str(video['title']))
            table_cols = ['title','domain_id','url','video_data', 'description']
            
            table_rows = [str(video['title']), is_domain_id, str(video['src']), str(video['video_data']), str(video['description'])]
            
            result = self.db.save( "dcgbn_isearch_videos"
                , table_cols
                , table_rows )
                
                
        else:
            try:
                table_cols = [
                    'title'
                    , 'video_data'
                    , 'description'
                ]
                table_rows = [
                    str(video['title'])
                    , str(video['video_data'])
                    , str(video['description'])
                ]
                result = self.db.update( "dcgbn_isearch_videos"
                    , table_cols
                    , table_rows
                    , exists[0][0] )
                    
                #print(result,"\n")
                
            except Exception as e:
                print( "IOP Error in: " + self.__class__.__name__ + " function is_addVideo() while updating the record: "+ str( e ) )
            
            #! Set the id obtain from the call to see if this domain already exists.
            result['id']=exists[0][0]
        
    def getDomains(self):
        return self.db.select(fromtable="dcgbn_inourplace_iop_domain") 
        
        
    def is_addDomain(self, domain, language='en-GB', menu={} ):
        ''' Saves data of a main domain.'''
        result = False
        url_parsed = urlparse( domain["url"] )
        exists = self.db.select("dcgbn_isearch_domains", select="id", where="host_url='"+url_parsed.netloc+"'" )
        #! Getting some page info from the meta data
        keywords = ""
        description = ""
        for meta in domain["info"]["meta"][0]:
            if str(meta["name"]).lower() == "keywords":
                keywords = str(meta["content"]).lower()
            if str(meta["name"]).lower() == "description":
                keywords = str(meta["content"]).lower()
        
        # Give the description some content if it contains nothing.
        if description == "":
            description = str(domain["info"]["title"])
        
        table_cols = ['host_url','title','description', 'keywords', 'site_abstraction', 'language', 'menu']
        table_rows = [url_parsed.netloc, str( domain["info"]["title"] ),description, keywords, str(domain["text"]), language, menu ]
        if len(exists) != 0:
            print( str( domain["url"] ) + " allready exists, updating domain...")
            
            #pprint(domain)
            try:
                result = self.db.update( "dcgbn_isearch_domains"
                    , table_cols
                    , table_rows
                    , exists[0][0] )
            except Exception as e:
                print( "Error in: " + self.__class__.__name__ + " function is_addDomain() while writing to the database: "+ str( e ) )
            
            #! Set the id obtain from the call to see if this domain already exists.
            result['id']=exists[0][0]
            
        else:
            print("Saving " + str( domain["url"] ) )
            result = self.db.save( "dcgbn_isearch_domains"
                , table_cols
                , table_rows )
        
        return result
    
        
    def getDomainRecord(self, domain):
        dr = self.db.select("dcgbn_isearch_domains", select="*", where="host_url='" + domain + "'" )
        
        return dr
        
    def saveDomainInfo(self, domain):
        ''' Saves data of a main domain.'''
        result = False
        print("Saving into database")
        url_parsed = urlparse(domain["url"])
        exists = self.db.select("dcgbn_inourplace_iop_domain", select="*", where="domain='"+url_parsed.netloc+"'" )
        print(exists)
        if len(exists) != 0:
            print("Domain is already existing...")
        else:
            print("Saving a new domain...")
            result = self.db.save( "dcgbn_inourplace_iop_domain"
                , ['domain','title','description']
                , [url_parsed.netloc, 'newtitle','newdescription'] )
            '''
            self.db.save( "dcgbn_inourplace_iop_domain"
                , ['title','description']
                , ['newtitle','newdescription'] )
            '''
        return result
        
        
        
def main():
    pass
        
if __name__ == "__main__":
    import sys    
    main()
    
