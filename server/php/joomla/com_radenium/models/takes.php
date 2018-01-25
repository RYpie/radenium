<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_radenium
 *
 * @copyright   Copyright (C) 2017 Andries Bron, Drachten, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 		
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 		
// JForm model description of Takes


// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');
				
				
/**
 * Radenium Model Project
 *
 * @since  0.0.1
 */
class RadeniumModelTakes extends JModelForm
{
		
    public function getForm($data = array(), $loadData = true)
    {
		
        $app = JFactory::getApplication('site');
		
        // Get the form.
        $form = $this->loadForm('radenium.takes', 'takes', array('control' => 'jform', 'load_data' => true));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

        
    /**
     * @name __construct
     * @desc Function description.
     */
    public function __construct()
    {
        // Construct the parent
        parent::__construct();
        return True;
    }

    
    private function startTake( $id, $data ) {
        $noterminal = " </dev/null >/dev/null 2>ffmpeg.log & echo $!";
        $ffmpeg = "/usr/local/bin/ffmpeg";
        
        mkdir("/Applications/MAMP/htdocs/radenium/media/com_radenium/media/takes/id_".$id."", 0757);
        $ffmpegcom = "-r 30 -f avfoundation -i 0:0 -pix_fmt yuv420p -s 640X320 -hls_flags round_durations -hls_time 3 -hls_init_time 3 /Applications/MAMP/htdocs/radenium/media/com_radenium/media/takes/id_".$id."/playlist.m3u8";
        
        ini_set('max_execution_time', 0);
        
        echo $ffmpeg." ".$ffmpegcom.$noterminal;

        $pid = exec($ffmpeg." ".$ffmpegcom.$noterminal, $out);
        
        return $pid;
    }
    
    private function stopTake( $pid ) {
        if ( $pid > 0 ) {
            $pid = exec("kill ".$pid, $out);
        }
        
        return true;
    }
    private function setPid($id, $pid) {
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Prepare table data:
        $fields = array(
            $db->quoteName('pid') . ' = ' . intval($pid)
        );
       // Conditions for which records should be updated:
        $conditions = array(
            $db->quoteName('id') .' = '. $id
        );
        // Prepare the insert query.
        $query->update($db->quoteName( '#__radenium_takes'))
            ->set($fields)
            ->where($conditions);
        // Set the query using our newly populated query object and execute it...
        $db->setQuery($query);
        $db->execute();
    }
            
    /**
     * @name save
     * @desc Creates a new form entry in the database.
     * @param data
     */
    public function save($data)
    {
        //Set the joomla platform user id:
        $data["user_id"] = JFactory::getUser()->id;
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Prepare table data:
        $columns = array(
            selectsource
            , vid
            , aid
            , files
            , resolution
            , format
            //, takedate
            , publish
            , state
            , pid
            , user_id

        );

        $values = array(
            intval($data["selectsource"])
            , intval($data["vid"])
            , intval($data["aid"])
            , intval($data["files"])
            , intval($data["resolution"])
            , intval($data["format"])
            //, $db->quote($data["takedate"])
            , intval($data["publish"])
            , intval($data["state"])
            , intval($data["pid"])
            , intval($data["user_id"])

        );

        // Prepare the insert query.
        $query
            ->insert($db->quoteName( '#__radenium_takes'))
            ->columns($db->quoteName( $columns))
            ->values(implode(',', $values));
        // Set the query using our newly populated query object and execute it.
        $db->setQuery($query);
        $db->execute();

        
        if ( intval($data["pid"]) < 1 ) {
            $lastRowId = $db->insertid();
            $data["pid"] = $this->startTake($lastRowId, $data);
            $this->setPid($lastRowId, $data["pid"]);
        }
        
        return True;
    }

        
    /**
     * @name edit
     * @desc Edits a new form entry in the database.
     * @param id
     * @param data
     */
    public function edit($id, $data)
    {
        
        if ( intval($data["state"]) == 2 ) {
            $this->stopTake($data["pid"]);
        }
        
        //Set the joomla platform user id:
        $data["user_id"] = JFactory::getUser()->id;
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Prepare table data:
        $fields = array(
            $db->quoteName('selectsource') . ' = ' . intval($data["selectsource"])
            , $db->quoteName('vid') . ' = ' . intval($data["vid"])
            , $db->quoteName('aid') . ' = ' . intval($data["aid"])
            , $db->quoteName('files') . ' = ' . intval($data["files"])
            , $db->quoteName('resolution') . ' = ' . intval($data["resolution"])
            , $db->quoteName('format') . ' = ' . intval($data["format"])
            //, $db->quoteName('takedate') . ' = ' . $db->quote($data["takedate"])
            , $db->quoteName('publish') . ' = ' . intval($data["publish"])
            , $db->quoteName('state') . ' = ' . intval($data["state"])
            , $db->quoteName('pid') . ' = ' . intval($data["pid"])
            , $db->quoteName('user_id') . ' = ' . intval($data["user_id"])

        );
        
       // Conditions for which records should be updated:
        $conditions = array(
            $db->quoteName('id') .' = '. $id
        );
        // Prepare the insert query.
        $query->update($db->quoteName( '#__radenium_takes'))
            ->set($fields)
			->where($conditions);
        // Set the query using our newly populated query object and execute it...
        $db->setQuery($query);
        $db->execute();

        return True;
    }

        
    /**
     * @name getAllEntries
     * @desc Retrieves all entries from the database.
     */
    public function getAllEntries()
    {
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Select all:
        $query->select('*');
        $query->from($db->quoteName('#__radenium_takes'));
        $db->setQuery($query);
        $db->execute();

        $results = $db->loadObjectList();

        return $results;
    }

        
    /**
     * @name getAllEntriesById
     * @desc Retrieves all entries from the database.
     */
    public function getAllEntriesById()
    {
        $results = array();
        if ( JFactory::getApplication()->input->get("takes_id") != "" )
        {
            $id = JFactory::getApplication()->input->get("takes_id");
            $conditions = array(
                $db->quoteName('id') . ' = '.$id
            );
            // Get a db connection:
            $db = JFactory::getDbo();
            // Create a new query object:
            $query = $db->getQuery(true);
            // Select all:
            $query->select('*');
            $query->from($db->quoteName('#__radenium_takes'));
            $query->where($conditions);
            $db->setQuery($query);
            $db->execute();

            $results = $db->loadObjectList();

        }
        return $results;
    }

        
    /**
     * @name getEntry
     * @desc Gets an entry by id from the database.
     * @param id=false
     */
    public function getEntry($id=false)
    {
        if ( ( $id == false )
        && ( JFactory::getApplication()->input->get("takes_id") != "" )
        )
        {
            $id = JFactory::getApplication()->input->get("takes_id");
        }
        if ( $id != false ) {
            // Get a db connection:
            $db = JFactory::getDbo();
            // Create a new query object:
            $query = $db->getQuery(true);
            // Get Entry by id:
            $conditions = array(
            $db->quoteName('id') . ' = '.$id
            );
            $query->select('*');
            $query->from($db->quoteName('#__radenium_takes'));
            $query->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $results = $db->loadObjectList();
        }
        else {
            $results = false;
        }

        return $results;
    }

        
    /**
     * @name getEntry_Entry_Id
     * @desc Gets an entry by the request variable $_REQUEST["takes_id"] from the database.
     */
    public function getEntry_Entry_Id()
    {
        $id = JFactory::getApplication()->input->get('takes_id');
        if ( $id != false ) {
            $results = $this->getEntry( $id );
        }
        else {
            $results = false;
        }

        return $results;
    }

        
    /**
     * @name delete
     * @desc Deletes an entries from the database.
     * @param id
     */
    public function delete($id)
    {
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Delete by id:
        $conditions = array(
            $db->quoteName('id') . ' = '.$id
        );
        $query->delete($db->quoteName('#__radenium_takes'));
        $query->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $results = $db->loadObjectList();

        return $results;
    }

        
    /**
     * @name gettake
     * @desc Function description.
     */
    public function gettake()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => take
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => selectsource
                    [type] => radio
                    [label] => COM_RADENIUM_FIELD_TASK_SELECT_SOURCE
                    [description] => COM_RADENIUM_FIELD_TASK_SELECT_SOURCE
                )

            [1] => Array
                (
                    [name] => vid
                    [type] => videodevices
                    [label] => COM_RADENIUM_FIELD_TASK_VID
                    [description] => COM_RADENIUM_FIELD_TASK_VID
                )

            [2] => Array
                (
                    [name] => aid
                    [type] => audiodevices
                    [label] => COM_RADENIUM_FIELD_TASK_AID
                    [description] => COM_RADENIUM_FIELD_TASK_AID
                )

            [3] => Array
                (
                    [name] => files
                    [type] => filesystem
                    [label] => COM_RADENIUM_FIELD_TASK_AID
                    [description] => COM_RADENIUM_FIELD_TASK_AID
                )

            [4] => Array
                (
                    [name] => resolution
                    [type] => screenresolution
                    [label] => COM_RADENIUM_FIELD_TASK_SCREEN_RESOLUTION
                    [description] => COM_RADENIUM_FIELD_TASK_SCREEN_RESOLUTION
                )

            [5] => Array
                (
                    [name] => format
                    [type] => mediaformat
                    [label] => COM_RADENIUM_FIELD_TASK_FORMAT
                    [description] => COM_RADENIUM_FIELD_TASK_FORMAT
                )

            [6] => Array
                (
                    [name] => takedate
                    [type] => calendar
                    [label] => COM_RADENIUM_FIELD_TAKE_DATE
                    [description] => COM_RADENIUM_FIELD_TAKE_DATE
                )

        )

)

*/

        

        return True;
    }

        
    /**
     * @name getlivepublish
     * @desc Function description.
     */
    public function getlivepublish()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => livepublish
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => publish
                    [type] => radio
                    [label] => COM_RADENIUM_FIELD_TASK_PUBLISH
                    [description] => COM_RADENIUM_FIELD_TASK_PUBLISH
                )

        )

)

*/

        

        return True;
    }

        
    /**
     * @name getrunningtake
     * @desc Function description.
     */
    public function getrunningtake()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => runningtake
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => state
                    [type] => radio
                    [label] => COM_RADENIUM_FIELD_TASK_STATE
                    [description] => COM_RADENIUM_FIELD_TASK_STATE
                )

            [1] => Array
                (
                    [name] => pid
                    [type] => number
                    [label] => COM_RADENIUM_FIELD_TASK_PID
                    [description] => COM_RADENIUM_FIELD_TASK_PID
                )

        )

)

*/

        

        return True;
    }

        
    /**
     * @name gethidden
     * @desc Function description.
     */
    public function gethidden()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => hidden
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => user_id
                    [type] => hidden
                    [label] => 
                    [description] => 
                )

        )

)

*/

        

        return True;
    }

}