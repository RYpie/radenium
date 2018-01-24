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


class RadeniumControllerEncdeck extends JControllerForm
{


    public function __construct($config=array())
    {
        parent::__construct($config);

    }


    public function save()
    {
        if ($this->checkToken()) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("encdeck");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $model->save($data);
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=encdeck&layout=default', false));
        }

    }


    public function modify()
    {
        if ($this->checkToken()) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("encdeck");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $model->edit( JFactory::getApplication()->input->get("encdeck_id"),$data );
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=encdeck&layout=default', false));
        }

    }


    public function edit()
    {
        if ($this->checkToken()) {
            // Not sure what to all in this function. Basically the data could be retrieved by the view, like with new form.
            $option = JFactory::getApplication()->input->get('option','string');
            $id = JFactory::getApplication()->input->get('encdeck_id');
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=encdeck&layout=edit&encdeck_id='.$id, false));
        }

    }


    public function delete()
    {
        if ($this->checkToken()) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("encdeck");
            $id = JFactory::getApplication()->input->get('encdeck_id');
            $model->delete($id);
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=encdeck&layout=default', false));
        }

    }


    public function execute($task)
    {
        //
        // Do you want to override the default controller?
        // $view = $this->getView("Encdeck","html");
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
