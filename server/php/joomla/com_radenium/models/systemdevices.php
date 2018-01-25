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
 		
// JForm model description of Systemdevices


// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');

include_once("components/com_radenium/models/phpffmpeg.php");		
				
/**
 * Radenium Model Project
 *
 * @since  0.0.1
 */
class RadeniumModelSystemdevices extends JModelForm
{
	private $ffmpeg;
	
    public function getForm($data = array(), $loadData = true)
    {
		
        $app = JFactory::getApplication('site');
		
        // Get the form.
        $form = $this->loadForm('radenium.systemdevices', 'systemdevices', array('control' => 'jform', 'load_data' => true));
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
        $this->ffmpeg = new RadeniumModelPhpffmpeg();
        echo getcwd();
        echo $this->ffmpeg->getMsg();
        $this->ffmpeg->getSystemDevices();
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
            , type
            , idstr
            , sysid

        );

        $values = array(
            $db->quote($data["name"])
            , $db->quote($data["type"])
            , $db->quote($data["idstr"])
            , $db->quote($data["sysid"])

        );

        // Prepare the insert query.
        $query
            ->insert($db->quoteName( '#__radenium_systemdevices'))
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
            , $db->quoteName('type') . ' = ' . $db->quote($data["type"])
            , $db->quoteName('idstr') . ' = ' . $db->quote($data["idstr"])
            , $db->quoteName('sysid') . ' = ' . $db->quote($data["sysid"])

        );

       // Conditions for which records should be updated:
        $conditions = array(
            $db->quoteName('id') .' = '. $id
        );
        // Prepare the insert query.
        $query->update($db->quoteName( '#__radenium_systemdevices'))
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
        $query->from($db->quoteName('#__radenium_systemdevices'));
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
        if ( JFactory::getApplication()->input->get("systemdevices_id") != "" )
        {
            $id = JFactory::getApplication()->input->get("systemdevices_id");
            $conditions = array(
                $db->quoteName('id') . ' = '.$id
            );
            // Get a db connection:
            $db = JFactory::getDbo();
            // Create a new query object:
            $query = $db->getQuery(true);
            // Select all:
            $query->select('*');
            $query->from($db->quoteName('#__radenium_systemdevices'));
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
        && ( JFactory::getApplication()->input->get("systemdevices_id") != "" )
        )
        {
            $id = JFactory::getApplication()->input->get("systemdevices_id");
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
            $query->from($db->quoteName('#__radenium_systemdevices'));
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
     * @desc Gets an entry by the request variable $_REQUEST["systemdevices_id"] from the database.
     */
    public function getEntry_Entry_Id()
    {
        $id = JFactory::getApplication()->input->get('systemdevices_id');
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
        $query->delete($db->quoteName('#__radenium_systemdevices'));
        $query->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $results = $db->loadObjectList();

        return $results;
    }

        
    /**
     * @name getsystemdevice
     * @desc Function description.
     */
    public function getsystemdevice()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => systemdevice
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => name
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_NAME_LABEL
                    [description] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_NAME_DESC
                )

            [1] => Array
                (
                    [name] => type
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_COMMAND_LABEL
                    [description] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_COMMAND_DESC
                )

            [2] => Array
                (
                    [name] => idstr
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_COMMAND_LABEL
                    [description] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_COMMAND_DESC
                )

            [3] => Array
                (
                    [name] => sysid
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_COMMAND_LABEL
                    [description] => COM_RADENIUM_FIELD_FFMPEG_TEMPLATE_COMMAND_DESC
                )

        )

)

*/

        

        return True;
    }

}
