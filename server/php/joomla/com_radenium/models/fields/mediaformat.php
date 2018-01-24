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
class JFormFieldMediaformat extends JFormFieldList {
	protected $type = 'Mediaformat';
	
	public function getOptions() {		
		$list_options = array(
		    "apple_hls" => "Apple HLS"
		    , "yessodothis"=>"make new field for vid size"
		);

		return $list_options;
	}
	
	public function getLabel() {
		return "Select Output Format";
	}
	
	// getLabel() left out
	public function getInput() {
		return JHtml::_('select.genericlist', $this->getOptions(), $this->name, null, 'value', 'text', $this->value, $this->id);

	}
}
