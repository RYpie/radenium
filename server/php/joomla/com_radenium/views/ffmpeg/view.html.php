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
 		

/**
 * HTML View class for the Radenium Component
 *
 * @since  0.0.1
 */
class RadeniumViewFfmpeg extends JViewLegacy
{
	function __construct()
	{
		parent::__construct();
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root() . 'media/com_radenium/css/style.css');
	}

	/**
	 * Display the Ffmpeg view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
	    $this->setLayout(JFactory::getApplication()->input->get("layout"));
		// Assign data to the view
		$this->msg = 'Ffmpeg message';

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
                $this->ffmpeg_id = JFactory::getApplication()->input->get("ffmpeg_id");
                break;

            case "commands":
                $this->commands_data = $this->get("commands");
                $this->form = $this->get("Form");
                break;



            default:
                $this->ffmpeg_entries = $this->get("AllEntries");
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

