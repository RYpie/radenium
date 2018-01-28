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
 		


JFormHelper::loadFieldClass('list');

//https://docs.joomla.org/Creating_a_custom_form_field_type
class JFormFieldFfmpeg extends JFormFieldList {
	protected $type = 'Ffmpeg';
	
	public function getOptions() {		
		$list_options = array();
		$user = Jfactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('id,name')->from('`#__radenium_ffmpeg`')->where('user_id = "'.$user->id.'"');
		$rows = $db->setQuery($query)->loadObjectlist();
		
		foreach($rows as $row){
			$list_options[strval($row->id)] = $row->name;
		}
		
		//Using the options causes the values of the list to start with 0.
		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $templates);
		//return $options;
        //Now the ID correspond with the database id's

		return $list_options;
	}
	
	public function getLabel() {
		return "Select Ffmpeg";
	}
	
	// getLabel() left out
	public function getInput() {
		return JHtml::_('select.genericlist', $this->getOptions(), $this->name, null, 'value', 'text', $this->value, $this->id);

	}
}
