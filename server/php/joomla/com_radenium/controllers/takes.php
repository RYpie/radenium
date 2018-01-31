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


class RadeniumControllerTakes extends JControllerForm
{


    public function __construct($config=array())
    {
        parent::__construct($config);

    }


    public function save($key = NULL, $urlVar = NULL)
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("takes");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $take_id = $model->save($data);
            // Now starting up the just created take...
            if (
            		($take_id !== false) // could store it to the database
            		&& ($take_id !== null)
            		&& (intval($data["pid"]) < 1) // take not already has a pid...
            	) {
            		// Get the systemdevice that have been selected for this take.
		            $model_systemdevices = $this->getModel("systemdevices");
		            $vid = $model_systemdevices->getEntry($data['vid'])[0];
		            $aid = $model_systemdevices->getEntry($data['aid'])[0];
					//start the take...
					// @todo this is via the model, I should do it here via the ffmpeg model. I think i can get that as well..
		            
		            $settings_model = $this->getModel("settings");
		            $data["settings"] = $settings_model->getSettings()[0];
		            
		            $model_ffmpeg = $this->getModel("ffmpeg");
		            
		            //$model_phpffmpeg = $this->getModel("phpffmpeg");
		            //$model_phpffmpeg = $this->getModel("phpffmpeg");
		            
		            $data["pid"] = $model_ffmpeg->startTake($take_id, $data, array("video"=>$vid, "audio"=>$aid));
		            
		            $model->setPid($take_id, $data["pid"]);

            }
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=edit&takes_id='.$take_id, false));
        }

    }

    
	public function togglepublishlive() {
		$option = JFactory::getApplication()->input->get('option','string');
		$take_id = JFactory::getApplication()->input->get('takes_id',false);
		
		$model = $this->getModel("takes");
		$data = $model->getEntry_Entry_Id()[0];

		$view = $this->getView( "takes", "raw" );
		$retVal = array();
		$retVal["take_id"] = $take_id;

		if (intval($data->publish) == 0) {
			$model_ffmpeg = $this->getModel("phpffmpeg");
			$linesout = $model_ffmpeg->publishLive($take_id);
			$retVal["output"] = $linesout;
			$model->setlivepublish(1,$take_id);
			$retVal["publish"] = 1;
			
		} else {
			$model->setlivepublish(0,$take_id);
			$retVal["publish"] = 0;
			
		}
		//echo "<a href=\"index.php?option=com_radenium&view=takes&format=raw&Itemid=105&task=publishlive&takes_id=58\">test</a>";
		
		$view->display_json($retVal);
	}
	
	
	public function stoptake() {
		$option = JFactory::getApplication()->input->get('option','string');
		$take_id = JFactory::getApplication()->input->get('takes_id',false);
		$model = $this->getModel("takes");
		$data = $model->getEntry_Entry_Id()[0];
		$retVal = array();
		if ( $data->pid != 0 ) {
			$model_ffmpeg = $this->getModel("phpffmpeg");
			$model_ffmpeg->stopTake($data->pid);
			$retVal["take_id"] = $take_id;
			$retVal["result"] = "OK";
			$retVal["msg"] = "Take stopped.";
			
		} else {
			$retVal["result"] = "NOK";
			$retVal["msg"] = "No proper pid found";
			
		}
		$view = $this->getView( "takes", "raw" );
		$view->display_json($retVal);
	}
    
    public function modify()
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("takes");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $model->edit( JFactory::getApplication()->input->get("takes_id"),$data );
            $model_ffmpeg = $this->getModel("phpffmpeg");
            
            if ( intval($data["state"]) == 2 ) {
            	$model_ffmpeg->stopTake($data["pid"]);
            }
            
            /**
             * @todo depending on the state I should redirect to a different layout, namely one where you can't
             * stop the take in case it was already stopped.
             */
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=default', false));
        }

    }


    public function edit($key = NULL, $urlVar = NULL)
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            // Not sure what to all in this function. Basically the data could be retrieved by the view, like with new form.
            $option = JFactory::getApplication()->input->get('option','string');
            $id = JFactory::getApplication()->input->get('takes_id')[0];
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=edit&takes_id='.$id, false));
        }

    }


    public function delete($multiple=array())
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("takes");
            $id_arr = JFactory::getApplication()->input->get('takes_id');
            foreach( $id_arr as $id ) {
            	'id_' . $id . DIRECTORY_SEPARATOR . 'sampledirtree';
            	$vid_url = DIRECTORY_SEPARATOR ."Applications"
            			. DIRECTORY_SEPARATOR ."MAMP"
						. DIRECTORY_SEPARATOR ."htdocs/radenium/media/com_radenium/media/takes/id_".$id;
				if ( file_exists($vid_url)) {
					$this->removeDir($vid_url);
				}
            	$model->delete($id);
            
            }
            
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=default', false));
        }
        
    }
    
    
    public function removeDir($dir) {
    	//$dir = 'samples' . DIRECTORY_SEPARATOR . 'sampledirtree';
    	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    	$files = new RecursiveIteratorIterator($it,
    			RecursiveIteratorIterator::CHILD_FIRST);
    	
    	foreach($files as $file) {
    		if ($file->isDir()){
    			rmdir($file->getRealPath());
    		
    		} else {
    			unlink($file->getRealPath());
    		
    		}
    	
    	}
    	rmdir($dir);
    
    }


    public function execute($task)
    {
        //
        // Do you want to override the default controller?
        // $view = $this->getView("Takes","html");
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
