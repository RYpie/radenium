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

	function display_json($data = array())
	{
		echo json_encode($data);
	}
	
	/**
	 * Display the Takes view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		die('Restricted access');
	}
}

