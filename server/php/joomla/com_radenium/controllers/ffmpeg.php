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
 		

				
// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');


class RadeniumControllerFfmpeg extends JControllerForm
{


    public function __construct($config=array())
    {
        parent::__construct($config);

    }


    public function save($key = NULL, $urlVar = NULL)
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("ffmpeg");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $model->save($data);
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=ffmpeg&layout=default', false));
        }

    }


    public function modify()
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("ffmpeg");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $model->edit( JFactory::getApplication()->input->get("ffmpeg_id"),$data );
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=ffmpeg&layout=default', false));
        }

    }


    public function edit($key = NULL, $urlVar = NULL)
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            // Not sure what to all in this function. Basically the data could be retrieved by the view, like with new form.
            $option = JFactory::getApplication()->input->get('option','string');
            $id = JFactory::getApplication()->input->get('ffmpeg_id');
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=ffmpeg&layout=edit&ffmpeg_id='.$id, false));
        }

    }


    public function delete()
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("ffmpeg");
            $id = JFactory::getApplication()->input->get('ffmpeg_id');
            $model->delete($id);
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=ffmpeg&layout=default', false));
        }

    }

	public function createthumbs() {
		$take_id = JFactory::getApplication()->input->get('takes_id');
		$model = $this->getModel("ffmpeg");
		
		$pos = JFactory::getApplication()->input->get('player_position');
		$tparts = explode(".", $pos);
		
		if (strpos($tparts[0],":") === false ) {
			$time_t = "00:00:".$tparts[0].".".$tparts[1];
		}
		
		
		$time_t="00:00:02.000";
		//echo $time_t."<br />";
		//, $time=$time_t
		$model->getThumbNails("media/com_radenium/media/takes/id_".$take_id."/", "playlist0.ts");
		$retVal = array("result"=>"thumbs created");
		$view = $this->getView( "ffmpeg", "raw" );
		
		$view->display_json($retVal);
	}
	
    public function execute($task)
    {
        //
        // Do you want to override the default controller?
        // $view = $this->getView("Ffmpeg","html");
        // $view->display();
        // Then comment the parent to be executed.
        // @attention: execution of parent overrides any view or layout variable set. 
        // parent::execute($task);

        return parent::execute($task);
    }

    public function checkToken($method = 'post', $redirect = true)
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
