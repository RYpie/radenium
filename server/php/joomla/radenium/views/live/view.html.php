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
 		

/**
 * HTML View class for the Radenium Component
 *
 * @since  0.0.1
 */
class RadeniumViewLive extends JViewLegacy
{
	function __construct()
	{
		parent::__construct();
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root() . 'media/com_ministry/css/style.css');
	}

	/**
	 * Display the Live view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
	    $this->setLayout(JFactory::getApplication()->input->get("layout"));
		// Assign data to the view
		$this->msg = 'Live message';

        switch( JFactory::getApplication()->input->get("layout") )
        {
            case "new":
                $this->new_data = $this->get("new");
                $this->form = $this->get("Form");
                break;

            case "edit":
                $this->edit_data = $this->get("edit");
                $this->form = $this->get("Form");
                $this->entry_data = $this->get("Entry_Entry_Id");
                $this->live_id = JFactory::getApplication()->input->get("live_id");
                break;

            case "settings":
                $this->settings_data = $this->get("settings");
                $this->form = $this->get("Form");
                break;

            default:
                $this->media = $this->get("CurrentMedia");
                $this->live_now = $this->get("CurrentLives");
                $this->live_entries = $this->get("AllEntries");
                break;
        }
				
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
				
			return false;
		}
		// Display the view
		parent::display($tpl);
	}
}

