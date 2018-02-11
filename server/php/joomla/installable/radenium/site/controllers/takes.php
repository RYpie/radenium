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
	private $mediadir="media/com_radenium/media/takes/";

    public function __construct($config=array())
    {
        parent::__construct($config);

    }

	private function getRadeniumSettings() {
		$settings_model = $this->getModel("settings");
		$set = $settings_model->getSettings()[0];
		return $set;
		
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
            		
            		if ( intval($data["selectsource"]) == 0 ) {
            			//0 = A recording from systemdevices.
	            		// Get the systemdevice that have been selected for this take.
			            $model_systemdevices = $this->getModel("systemdevices");
			            $vid = $model_systemdevices->getEntry($data['vid'])[0];
			            $aid = $model_systemdevices->getEntry($data['aid'])[0];
						//start the take...
						// @todo this is via the including the model, I should do it  via the joomla method of loading the ffmpeg model...
			            $settings_model = $this->getModel("settings");
			            $data["settings"] = $settings_model->getSettings()[0];
			            $model_ffmpeg = $this->getModel("ffmpeg");
			            $data["pid"] = $model_ffmpeg->startTake($take_id, $data, array("video"=>$vid, "audio"=>$aid));
			            $model->setPid($take_id, $data["pid"]);

			            JFactory::getApplication()->enqueueMessage("Take has started with PID ".$data["pid"], $message="message");
			            
			            
            		} else if ( intval($data["selectsource"]) == 1 ) {
            			// 1 = Files from a volume://radenium/raw/ directory
            			// Those files next should be encoded into hls.
            			
            			JFactory::getApplication()->enqueueMessage("Copying files from USB sticks has not yet been implemented", $message="error");
            			
            		} else if ( intval($data["selectsource"]) == 2 ) {
            			// 2 = This guy is uploading a movie! into the media/com_radenium/media/raw/ directory
            			// Those files next should be encoded into hls.
            			
            			JFactory::getApplication()->enqueueMessage("Uploading files has not yet been implemented", $message="error");
            			
            		}

            }
            $this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=edit&takes_id='.$take_id, false));
        }

    }
	
    
    /**
     * @desc To be called by a javascript AJAX function.
     */
	public function publishlive() {
		//http://localhost:8888/radenium/index.php?option=com_radenium&amp;view=takes&amp;format=raw&amp;task=publishlive&amp;takes_id=188
		
		$option = JFactory::getApplication()->input->get('option','string');
		$take_id = JFactory::getApplication()->input->get('takes_id',false);
		
		$model = $this->getModel("takes");
		$data = (array)$model->getEntry_Entry_Id()[0];
		//print_r($data);
		$view = $this->getView( "takes", "raw" );
		$retVal = array();
		$retVal["take_id"] = $take_id;
		$model_ffmpeg = $this->getModel("ffmpeg");
		
		$data["settings"] = $this->getRadeniumSettings();

		if (intval($data['publish']) == 0) {
			$linesout = $model_ffmpeg->publishLive($take_id, $options=$data);
			$retVal["output"] = $linesout;
			$retVal["pid"] = $linesout[0];
			$model->setlivepublish(1,$retVal);
			$retVal["publish"] = 1;
			
		} else {			
			$linesout = $model_ffmpeg->stopProcessWithId($data['live_pid']);
			$model->setlivepublish(0,$retVal);
			$retVal["publish"] = 0;
			
		}
		//echo "<a href=\"index.php?option=com_radenium&view=takes&format=raw&Itemid=105&task=publishlive&takes_id=58\">test</a>";
			
		$view->display_json($retVal);

	}
	
	
	/**
	 * @desc To be called by a javascript AJAX function.
	 */
	public function stoptake() {
		$option = JFactory::getApplication()->input->get('option','string');
		$take_id = JFactory::getApplication()->input->get('takes_id',false);
		$model = $this->getModel("takes");
		
		$data = $model->getEntry_Entry_Id()[0];
		$retVal = array();
		if ( $data->pid != 0 ) {
			$model_ffmpeg = $this->getModel("ffmpeg");
			$model_ffmpeg->stopTake($data->pid);
			$retVal["take_id"] = $take_id;
			$retVal["result"] = "OK";
			$retVal["msg"] = "Take stopped.";
			$model->stopTake($take_id);
			
		} else {
			$retVal["result"] = "NOK";
			$retVal["msg"] = "No proper pid found";
			
		}
		$this->createFinalPlaylist();
		
		$view = $this->getView( "takes", "raw" );
		$view->display_json($retVal);
	}
    
	
    public function modify()
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("takes");
            $data = JFactory::getApplication()->input->get('jform','', 'array');
            $model->editNotes( JFactory::getApplication()->input->get("takes_id"), $data );
            
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
            
            // If at least one was selected when we come here:
            if ( $id != "" ) {
            	$this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=edit&takes_id='.$id, false));
        
            } else {
            	$this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=default', false));
            	
            }
        
        }

    }
	
    
    public function createFinalPlaylist() {
    	$model_system = $this->getModel("phpsystem");
    	
    	$media = array();
    	//    	$this->getDirContents($this->mediadir."id_".$take_id, $media, $findfile ="playlist.m3u8");
    	
    	
    }
    
    
    public function m3u8status() {
    	$option = JFactory::getApplication()->input->get('option','string');
    	$take_id = JFactory::getApplication()->input->get('takes_id',false);
    	
    	$media=array();


    	if ( count($media) == 0 ){
    		$retVal["status"] = "None";
    		
    	} else {
    		
    		$retVal["status"] = "Running";
    		
    	}
    	$view = $this->getView( "takes", "raw" );
    	$view->display_json($retVal);
    	
    }
    
    
    /*
     * @todo should by done through the system model.
     */
    private function getDirContents($dir, &$results = array(), $findfile = False){

    	if(substr($dir, -1) == '/') {
    		$dir = substr($dir, 0, -1);
    	}
    	$files = scandir($dir);
    	foreach($files as $key => $value){
    		$path = $dir.DIRECTORY_SEPARATOR.$value;//realpath($dir.DIRECTORY_SEPARATOR.$value);
    		
    		if(!is_dir($path)) {
    			if ( $findfile !== False ){
    				if ($value == $findfile) {
    					$results[] = $path;
    				}
    			} else {
    				$results[] = $path;
    			}
    		} else if($value != "." && $value != ".." && $value != ".DS_Store") {
    			$this->getDirContents($path, $results, $findfile);
    			if ( $findfile == False ){
    				$results[] = $path;
    			}
    		}
    	}
    	
    	return $results;
    }

    public function delete($multiple=array())
    {
        if ($this->checkToken($method = 'post', $redirect = true)) {
            $option = JFactory::getApplication()->input->get('option','string');
            $model = $this->getModel("takes");
            $id_arr = JFactory::getApplication()->input->get('takes_id');
            
            // If at least one was selected when we come here:
            if ( count($id_arr) > 0) {
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
	        
	        } else {
	        	$this->setRedirect( JRoute::_('index.php?option='.$option.'&view=takes&layout=default', false));
	        	
	        }
        
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
