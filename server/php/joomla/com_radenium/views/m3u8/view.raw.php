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


include_once 'components/com_radenium/views/m3u8/m3u8.php';


/**
 * HTML View class for the Radenium Component
 *
 * @since  0.0.1
 */
class RadeniumViewM3u8 extends JViewLegacy
{
	
	
	function controlout($status) {
		header("Content-Type: application/json");
		echo json_encode($status);
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
		$doc =& JFactory::getDocument();
		$doc->setMimeEncoding('application/x-mpegURL');
		
		$m3u8 = new m3u8(JFactory::getApplication()->input->get('take_id',false));

		header("Cache-Control: no-store, no-cache,must-revalidate");
		header("Content-Type: application/x-mpegURL");
		header("Cache-Control: post-check=0, pre-check=0",false);
		header("Pragma: no-cache");
		//header('Content-Length: ' . sizeof($m3u8->m3u8_src.PHP_EOL));
		echo $m3u8->m3u8_src;

	}
}

