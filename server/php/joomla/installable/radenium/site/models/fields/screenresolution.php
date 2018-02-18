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
 		


JFormHelper::loadFieldClass('list');

//https://docs.joomla.org/Creating_a_custom_form_field_type
class JFormFieldScreenResolution extends JFormFieldList {
	protected $type = 'ScreenResolution';
	
	public function getOptions() {		
		$list_options = array(
			0 => "Screen Resolution"
			, 1 => "025_VGA 340x210"
			, 2 => "VGA 680x420"
		    , 3 => "XGA 1024x768"
			, 4 => "WXGA 1280x720"
		    , 5 => "025_HD 960x540"
			, 6 => "HD 1920x1080"
		);
		
		/*
		$user = Jfactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('id,name')->from('`#__radenium_live`')->where('user_id = "'.$user->id.'"');
		$rows = $db->setQuery($query)->loadObjectlist();
		
		foreach($rows as $row){
			$list_options[strval($row->id)] = $row->name;
		}
		*/
		//Using the options causes the values of the list to start with 0.
		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $templates);
		//return $options;
        //Now the ID correspond with the database id's

		return $list_options;
	}
	
	public function getLabel() {
		return "Select Output Resolution";
	}
	
	// getLabel() left out
	public function getInput() {
		return JHtml::_('select.genericlist', $this->getOptions(), $this->name, null, 'value', 'text', $this->value, $this->id);

	}
}
