<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_radenium
 *
 * @copyright   Copyright (C) 2017 Andries Bron. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 		
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 		

/**
 *
 * @desc Intantiates controllers by view name. If no view is given, the default component controller is instantiated.
 */
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
				
				
$japp = JFactory::getApplication();
				
// fetch the view
$view = $japp->input->get('view');

if ( 
		($view == 'radenium')
		//|| ($view== 'm3u8')
		) {
	// If view is default component, then include the default controller and reset the $view.
	require_once JPATH_COMPONENT.DS.'controller.php';
	$view = '';
	
} else {
	// Use the view to fetch the right controller
	require_once JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php';
	
}

// initiate the contoller class and execute the controller
$controllerClass = 'radeniumController'.ucfirst($view);
				
$controller = new $controllerClass;
				
// Perform the Request task
$controller->execute($japp->input->getCmd('task','display'));
				
// Redirect if set by the controller
$controller->redirect();
