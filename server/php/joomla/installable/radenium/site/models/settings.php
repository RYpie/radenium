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
 		
// JForm model description of Settings


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
class RadeniumModelSettings extends JModelForm
{
		
    public function getForm($data = array(), $loadData = true)
    {
		
        $app = JFactory::getApplication('site');
		
        // Get the form.
        $form = $this->loadForm('radenium.settings', 'settings', array('control' => 'jform', 'load_data' => true));
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
            name
            , video_out_size
            , frame_rate
            , remote_url
            , remote_user
            , remote_password
            , rtsp_enable
            , rtsp_url
            , rtsp_user
            , rtsp_password
            , rtsp_port
            , rtsp_key
            , user_id

        );

        $values = array(
            $db->quote($data["name"])
            , $db->quote($data["video_out_size"])
            , intval($data["frame_rate"])
            , $db->quote($data["remote_url"])
            , $db->quote($data["remote_user"])
            , $db->quote($data["remote_password"])
            , intval($data["rtsp_enable"])
            , $db->quote($data["rtsp_url"])
            , $db->quote($data["rtsp_user"])
            , $db->quote($data["rtsp_password"])
            , intval($data["rtsp_port"])
            , $db->quote($data["rtsp_key"])
            , intval($data["user_id"])

        );

        // Prepare the insert query.
        $query
            ->insert($db->quoteName( '#__radenium_settings'))
            ->columns($db->quoteName( $columns))
            ->values(implode(',', $values));
        // Set the query using our newly populated query object and execute it.
        $db->setQuery($query);
        $db->execute();

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
        //Set the joomla platform user id:
        $data["user_id"] = JFactory::getUser()->id;
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Prepare table data:
        $fields = array(
            $db->quoteName('name') . ' = ' . $db->quote($data["name"])
            , $db->quoteName('video_out_size') . ' = ' . $db->quote($data["video_out_size"])
            , $db->quoteName('frame_rate') . ' = ' . intval($data["frame_rate"])
            , $db->quoteName('remote_url') . ' = ' . $db->quote($data["remote_url"])
            , $db->quoteName('remote_user') . ' = ' . $db->quote($data["remote_user"])
            , $db->quoteName('remote_password') . ' = ' . $db->quote($data["remote_password"])
            , $db->quoteName('rtsp_enable') . ' = ' . intval($data["rtsp_enable"])
            , $db->quoteName('rtsp_url') . ' = ' . $db->quote($data["rtsp_url"])
            , $db->quoteName('rtsp_user') . ' = ' . $db->quote($data["rtsp_user"])
            , $db->quoteName('rtsp_password') . ' = ' . $db->quote($data["rtsp_password"])
            , $db->quoteName('rtsp_port') . ' = ' . intval($data["rtsp_port"])
            , $db->quoteName('rtsp_key') . ' = ' . $db->quote($data["rtsp_key"])
            , $db->quoteName('user_id') . ' = ' . intval($data["user_id"])

        );

       // Conditions for which records should be updated:
        $conditions = array(
            $db->quoteName('id') .' = '. $id
        );
        // Prepare the insert query.
        $query->update($db->quoteName( '#__radenium_settings'))
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
        $query->from($db->quoteName('#__radenium_settings'));
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
        if ( JFactory::getApplication()->input->get("settings_id") != "" )
        {
            $id = JFactory::getApplication()->input->get("settings_id");
            $conditions = array(
                $db->quoteName('id') . ' = '.$id
            );
            // Get a db connection:
            $db = JFactory::getDbo();
            // Create a new query object:
            $query = $db->getQuery(true);
            // Select all:
            $query->select('*');
            $query->from($db->quoteName('#__radenium_settings'));
            $query->where($conditions);
            $db->setQuery($query);
            $db->execute();

            $results = $db->loadObjectList();

        }
        return $results;
    }

    public function getSettings()
    {

    		// Get a db connection:
    		$db = JFactory::getDbo();
    		// Create a new query object:
    		$query = $db->getQuery(true);
    		// Get Entry by id:
    		$conditions = array(
    				$db->quoteName('user_id') . ' = '.JFactory::getUser()->id
    		);
    		$query->select('*');
    		$query->from($db->quoteName('#__radenium_settings'));
    		$query->where($conditions);
    		$db->setQuery($query);
    		$db->execute();
    		$results = $db->loadAssocList();

    	
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
        && ( JFactory::getApplication()->input->get("settings_id") != "" )
        )
        {
            $id = JFactory::getApplication()->input->get("settings_id");
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
            $query->from($db->quoteName('#__radenium_settings'));
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
     * @desc Gets an entry by the request variable $_REQUEST["settings_id"] from the database.
     */
    public function getEntry_Entry_Id()
    {
        $id = JFactory::getApplication()->input->get('settings_id');
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
        $query->delete($db->quoteName('#__radenium_settings'));
        $query->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $results = $db->loadObjectList();

        return $results;
    }

        
    /**
     * @name getchannel
     * @desc Function description.
     */
    public function getchannel()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => channel
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => name
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_CHANNEL_NAME_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_CHANNEL_NAME_DESC
                )

        )

)

*/

        

        return True;
    }

        
    /**
     * @name getffmpeg
     * @desc Function description.
     */
    public function getffmpeg()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => ffmpeg
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => video_out_size
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_FFMPEG_VOUT_SIZE_LABEL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_FFMPEG_VOUT_SIZE_DESC
                )

            [1] => Array
                (
                    [name] => frame_rate
                    [type] => number
                    [label] => COM_RADENIUM_FIELD_SETTINGS_FFMPEG_FRAME_RATE_LABEL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_FFMPEG_FRAME_RATE_NAME_DESC
                )

        )

)

*/

        

        return True;
    }

        
    /**
     * @name getremote
     * @desc Function description.
     */
    public function getremote()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => remote
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => remote_url
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_REMOTE_URL_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_REMOTE_URL_DESC
                )

            [1] => Array
                (
                    [name] => remote_user
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_REMOTE_USER_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_REMOTE_USER_DESC
                )

            [2] => Array
                (
                    [name] => remote_password
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_REMOTE_PASSWORD_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_REMOTE_PASSWORD_DESC
                )

        )

)

*/

        

        return True;
    }

        
    /**
     * @name getrtsp_server
     * @desc Function description.
     */
    public function getrtsp_server()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => rtsp_server
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => rtsp_enable
                    [type] => radio
                    [label] => COM_RADENIUM_FIELD_SETTINGS_RTSP_ENABLE_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_RTSP_ENABLE_DESC
                )

            [1] => Array
                (
                    [name] => rtsp_url
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_RTSP_URL_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_RTSP_URL_DESC
                )

            [2] => Array
                (
                    [name] => rtsp_user
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_RTSP_USER_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_RTSP_USER_DESC
                )

            [3] => Array
                (
                    [name] => rtsp_password
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_RTSP_PASSWORD_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_RTSP_PASSWORD_DESC
                )

            [4] => Array
                (
                    [name] => rtsp_port
                    [type] => number
                    [label] => COM_RADENIUM_FIELD_SETTINGS_RTSP_PORT_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_RTSP_PORT_DESC
                )

            [5] => Array
                (
                    [name] => rtsp_key
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_SETTINGS_RTSP_KEY_LBL
                    [description] => COM_RADENIUM_FIELD_SETTINGS_RTSP_KEY_DESC
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
