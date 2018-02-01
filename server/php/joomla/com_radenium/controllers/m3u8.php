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
 		

				
// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');


class RadeniumControllerM3u8 extends JControllerForm
{


    public function __construct($config=array())
    {
        parent::__construct($config);

    }


    public function execute($task)
    {
        //
        // Do you want to override the default controller?
        // $view = $this->getView("Live","html");
        // $view->display();
        // Then comment the parent to be executed.
        // @attention: execution of parent overrides any view or layout variable set. 
        // parent::execute($task);

        return parent::execute($task);
    }

    public function checkToken()
    {
        $token = JSession::getFormToken();
        
        if ( !$token || !JFactory::getApplication()->input->get($token, null, 'alnum') )
            return False;
        else
            return True;
    }

    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
		{
			return parent::getModel($name, $prefix, array('ignore_request' => false));
		}
}
