#!/usr/bin/env python
import mysql.connector
from mysql.connector import (connection)
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

_PREFIX = ""

'''
from mysql.connector import (connection)
cnx = connection.MySQLConnection(user='root', password='root', host='localhost', port='8889', database='iop')
cursor = cnx.cursor()
query = ("SELECT * FROM dcgbn_inourplace_iop_domain")
cursor.execute(query)
cursor.fetchall()
'''

class mod_dbaccess(object):
    def __init__( self, dbname="iop" ):
        # Open database connection
        self.cnx = connection.MySQLConnection(user='root', password='root', host='localhost', port='8889', database=dbname)
        

        
    def select(self, fromtable, select="*", where=""):
        cursor = self.cnx.cursor()
        
        query = ("SELECT " + select + " FROM "+str(fromtable))
        if where != "":
            query += " WHERE " + where
            
        #print("QUERY=", query)
        
        cursor.execute(query)
        result = cursor.fetchall()
        cursor.close()
        return result;
        
        
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
        
    def save(self, totable, columns, values):
        retval = {}
        cursor = self.cnx.cursor()
        cols = ", ".join(columns)
        vals = ""
        for v in values:
            if vals != "":
                vals +=", "
            vals += "%s"
            
            
        add_domain = ("INSERT INTO " + str(totable) + " "
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
    """
    @todo: primary keyword en secondary keywords 
    @todo: stukje text meenemen waar keywoord in is gevonden. 
    @todo: visualiseren van de resultaten, de sitebouwer dan titels opvragen van paginas waar links naar verwijzen. 
    """
    
    def __init__( self ):  
        try:
            self.db = dbaccess.dBAccess()

        except:
            pass
            
            
    def getDomains(self, notCrawledAfter=None):
        '''
        param notCrawledAfter date string 
        Returns a list of url's that have to be crawled, should be those that have not
        recently been crawled.
        
        '''
        return {}
            
    def saveSearchResults( self, domain_info, domain_index=None, domain_spy=None ):
        
        '''
        pprint(domain_index["topics"])
        
        #! Getting all the interesting media links here, including podcast links.
        
        social_media_links=[]
        if "feeds" in domain_index["topics"]:
            social_media_links.append(domain_index["topics"]["feeds"])
        if "social" in domain_index["topics"]:
            social_media_links.append(domain_index["topics"]["social"])
        if "syndicated_content" in domain_index["topics"]:
            social_media_links.append(domain_index["topics"]["syndicated_content"])
        '''
            
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
                print( "IOP Error in: " + self.__class__.__name__ + " function is_addDomain() while writing to the database: "+ str( e ) )
            
            #! Set the id obtain from the call to see if this domain already exists.
            result['id']=exists[0][0]
            
        else:
            print("Saving " + str( domain["url"] ) )
            result = self.db.save( "dcgbn_isearch_domains"
                , table_cols
                , table_rows )
        
        return result
        
        
    def saveVideos(self, domainRecord, videos):
        '''creates articles in joomla'''
        id = domainRecord[0]
        print("check if video exists")
        newvideos = []
        for v in videos:
        
            newvideos.append(v)
            #! query database
            #! If a result available then pop video from list
            
        i = 0
        #! Create articles of each new video
        for nv in newvideos:
            print("creating articles")
            alias = nv["src"].replace("&","")
            alias = alias.replace("http://","")
            alias = alias.replace("https://","")
            alias = alias.replace("www.","")
            alias = alias.replace("=","-")
            alias = alias.replace(".","-")
            alias = alias.replace("/","-")
            
            _JOOMLA_ARTICLE_RECORD["alias"] = alias
            _JOOMLA_ARTICLE_RECORD["introtext"] = "{iop type=iframe url=" + nv["src"] + "}{/iop}"
            
            if "page" in nv:
                if "info" in nv["page"]:
                    if "title" in nv["info"]:
                        _JOOMLA_ARTICLE_RECORD["title"] = nv["info"]["title"]
                        _JOOMLA_ARTICLE_RECORD["metadesc"] = nv["info"]["title"]
            
            #_JOOMLA_ARTICLE_RECORD["introtext"] = "test text"
            cols = []
            vals = []
            for key in _JOOMLA_ARTICLE_RECORD:
                cols.append("`"+key+"`")
                vals.append(_JOOMLA_ARTICLE_RECORD[key])
                
            cols1 = []
            vals1 = []
            for key in _IOP_VIDEO_RECORD:
                cols1.append(key)
                vals1.append(_IOP_VIDEO_RECORD[key])
            
            if i == 0:
                #pprint( _JOOMLA_ARTICLE_RECORD )
                print ( self.db.save( _JOOMLA_ARTICLE_TABLE, cols, vals ) )
                
                #print( cols1, vals1)
                #print ( self.db.save( _IOP_VIDEO_TABLE, cols1, vals1 ) )
                '''
                result = self.db.save( "dcgbn_inourplace_iop_videos"
                , ['aid','type', 'description', 'user_id']
                , ['1', '0', 'bla', '0'] )
                '''
                
            newvideos[i]["article_id"] = 16 + i
            #! Add article ID
            i += 1
        
        #! Save all new videos:
        pprint( newvideos )
        '''
        result = self.db.save( "dcgbn_inourplace_iop_videos"
            , ['domain_id','article_id','type', 'description', 'user_id']
            , [id, 'newtitle','newdescription'] )
        '''
        
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
    
