<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_radenium
 *
 * @copyright   Copyright (C) 2017 Andries Bron, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 		
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 		
// JForm model description of Encdck_encodetasks


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
class RadeniumModelEncdck_encodetasks extends JModelForm
{
		
    public function getForm($data = array(), $loadData = true)
    {
		
        $app = JFactory::getApplication('site');
		
        // Get the form.
        $form = $this->loadForm('radenium.encdck_encodetasks', 'encdck_encodetasks', array('control' => 'jform', 'load_data' => true));
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
            publish
            , state
            , vid
            , aid
            , prog_id_str
            , format
            , user_id

        );

        $values = array(
            intval($data["publish"])
            , intval($data["state"])
            , $db->quote($data["vid"])
            , $db->quote($data["aid"])
            , $db->quote($data["prog_id_str"])
            , $db->quote($data["format"])
            , intval($data["user_id"])

        );

        // Prepare the insert query.
        $query
            ->insert($db->quoteName( '#__radenium_encdck_encodetasks'))
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
            $db->quoteName('publish') . ' = ' . intval($data["publish"])
            , $db->quoteName('state') . ' = ' . intval($data["state"])
            , $db->quoteName('vid') . ' = ' . $db->quote($data["vid"])
            , $db->quoteName('aid') . ' = ' . $db->quote($data["aid"])
            , $db->quoteName('prog_id_str') . ' = ' . $db->quote($data["prog_id_str"])
            , $db->quoteName('format') . ' = ' . $db->quote($data["format"])
            , $db->quoteName('user_id') . ' = ' . intval($data["user_id"])

        );

       // Conditions for which records should be updated:
        $conditions = array(
            $db->quoteName('id') .' = '. $id
        );
        // Prepare the insert query.
        $query->update($db->quoteName( '#__radenium_encdck_encodetasks'))
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
        $query->from($db->quoteName('#__radenium_encdck_encodetasks'));
        $db->setQuery($query);
        $db->execute();

        $results = $db->loadObjectList();

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
        && ( JFactory::getApplication()->input->get("encdck_encodetasks_id") != "" )
        )
        {
            JFactory::getApplication()->input->get("encdck_encodetasks_id");
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
            $query->from($db->quoteName('#__radenium_encdck_encodetasks'));
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
     * @desc Gets an entry by the request variable $_REQUEST["encdck_encodetasks_id"] from the database.
     */
    public function getEntry_Entry_Id()
    {
        $id = JFactory::getApplication()->input->get('encdck_encodetasks_id');
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
        $query->delete($db->quoteName('#__radenium_encdck_encodetasks'));
        $query->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $results = $db->loadObjectList();

        return $results;
    }

        
    /**
     * @name gettask
     * @desc Function description.
     */
    public function gettask()
    {
        // Returns a particular fieldset to render as form. 

        // should return a form with only the fieldset 
/*Array
(
    [name] => task
    [variables] => Array
        (
            [0] => Array
                (
                    [name] => publish
                    [type] => number
                    [label] => COM_RADENIUM_FIELD_TASK_PUBLISH
                    [description] => COM_RADENIUM_FIELD_TASK_PUBLISH
                )

            [1] => Array
                (
                    [name] => state
                    [type] => number
                    [label] => COM_RADENIUM_FIELD_TASK_STATE
                    [description] => COM_RADENIUM_FIELD_TASK_STATE
                )

            [2] => Array
                (
                    [name] => vid
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_TASK_VID
                    [description] => COM_RADENIUM_FIELD_TASK_VID
                )

            [3] => Array
                (
                    [name] => aid
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_TASK_AID
                    [description] => COM_RADENIUM_FIELD_TASK_AID
                )

            [4] => Array
                (
                    [name] => prog_id_str
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_TASK_PROG_ID_STR
                    [description] => COM_RADENIUM_FIELD_TASK_PROG_ID_STR
                )

            [5] => Array
                (
                    [name] => format
                    [type] => text
                    [label] => COM_RADENIUM_FIELD_TASK_FORMAT
                    [description] => COM_RADENIUM_FIELD_TASK_FORMAT
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
