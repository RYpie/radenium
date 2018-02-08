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
 		
// JForm model description of Ffmpeg


// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');
				
				
/**
 * Radenium Model Project
 *
 * @since  0.0.1
 * 
 * @desc help links:
 * https://www.wowza.com/docs/how-to-use-ffmpeg-with-wowza-media-server-mpeg-ts#restreamrtsp
 * 
 */
class RadeniumModelFfmpeg extends JModelForm
{
	protected $noterminal = " </dev/null >/dev/null 2>ffmpeg.log & echo $!";
	
    public function getForm($data = array(), $loadData = true)
    {
		
        $app = JFactory::getApplication('site');
		
        // Get the form.
        $form = $this->loadForm('radenium.ffmpeg', 'ffmpeg', array('control' => 'jform', 'load_data' => true));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

        
    /**
     * @name __construct
     * @desc Function description.
     */
    public function __construct()
    {
        // Construct the parent
        parent::__construct();
        return True;
    }

        
    /**
     * @name save
     * @desc Creates a new form entry in the database.
     * @param data
     */
    public function save($data)
    {
        //Set the joomla platform user id:
        $data["user_id"] = JFactory::getUser()->id;
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Prepare table data:
        $columns = array(
            name
            , command
            , platform
            , category

        );

        $values = array(
            $db->quote($data["name"])
            , $db->quote($data["command"])
            , $db->quote($data["platform"])
            , intval($data["category"])

        );

        // Prepare the insert query.
        $query
            ->insert($db->quoteName( '#__radenium_ffmpeg'))
            ->columns($db->quoteName( $columns))
            ->values(implode(',', $values));
        // Set the query using our newly populated query object and execute it.
        $db->setQuery($query);
        $db->execute();

        return True;
    }

        
    /**
     * @name edit
     * @desc Edits a new form entry in the database.
     * @param id
     * @param data
     */
    public function edit($id, $data)
    {
        //Set the joomla platform user id:
        $data["user_id"] = JFactory::getUser()->id;
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Prepare table data:
        $fields = array(
            $db->quoteName('name') . ' = ' . $db->quote($data["name"])
            , $db->quoteName('command') . ' = ' . $db->quote($data["command"])
            , $db->quoteName('platform') . ' = ' . $db->quote($data["platform"])
            , $db->quoteName('category') . ' = ' . intval($data["category"])

        );

       // Conditions for which records should be updated:
        $conditions = array(
            $db->quoteName('id') .' = '. $id
        );
        // Prepare the insert query.
        $query->update($db->quoteName( '#__radenium_ffmpeg'))
            ->set($fields)
			->where($conditions);
        // Set the query using our newly populated query object and execute it...
        $db->setQuery($query);
        $db->execute();

        return True;
    }

        
    /**
     * @name getAllEntries
     * @desc Retrieves all entries from the database.
     */
    public function getAllEntries()
    {
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Select all:
        $query->select('*');
        $query->from($db->quoteName('#__radenium_ffmpeg'));
        $db->setQuery($query);
        $db->execute();

        $results = $db->loadObjectList();

        return $results;
    }

        
    /**
     * @name getAllEntriesById
     * @desc Retrieves all entries from the database.
     */
    public function getAllEntriesById()
    {
        $results = array();
        if ( JFactory::getApplication()->input->get("ffmpeg_id") != "" )
        {
            $id = JFactory::getApplication()->input->get("ffmpeg_id");
            $conditions = array(
                $db->quoteName('id') . ' = '.$id
            );
            // Get a db connection:
            $db = JFactory::getDbo();
            // Create a new query object:
            $query = $db->getQuery(true);
            // Select all:
            $query->select('*');
            $query->from($db->quoteName('#__radenium_ffmpeg'));
            $query->where($conditions);
            $db->setQuery($query);
            $db->execute();

            $results = $db->loadObjectList();

        }
        return $results;
    }

        
    /**
     * @name getEntry
     * @desc Gets an entry by id from the database.
     * @param id=false
     */
    public function getEntry($id=false)
    {
        if ( ( $id == false )
        && ( JFactory::getApplication()->input->get("ffmpeg_id") != "" )
        )
        {
            $id = JFactory::getApplication()->input->get("ffmpeg_id");
        }
        if ( $id != false ) {
            // Get a db connection:
            $db = JFactory::getDbo();
            // Create a new query object:
            $query = $db->getQuery(true);
            // Get Entry by id:
            $conditions = array(
            $db->quoteName('id') . ' = '.$id
            );
            $query->select('*');
            $query->from($db->quoteName('#__radenium_ffmpeg'));
            $query->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $results = $db->loadAssocList();
        }
        else {
            $results = false;
        }

        return $results;
    }

        
    /**
     * @name getEntry_Entry_Id
     * @desc Gets an entry by the request variable $_REQUEST["ffmpeg_id"] from the database.
     */
    public function getEntry_Entry_Id()
    {
        $id = JFactory::getApplication()->input->get('ffmpeg_id');
        if ( $id != false ) {
            $results = $this->getEntry( $id );
        }
        else {
            $results = false;
        }

        return $results;
    }

        
    /**
     * @name delete
     * @desc Deletes an entries from the database.
     * @param id
     */
    public function delete($id)
    {
        // Get a db connection:
        $db = JFactory::getDbo();
        // Create a new query object:
        $query = $db->getQuery(true);
        // Delete by id:
        $conditions = array(
            $db->quoteName('id') . ' = '.$id
        );
        $query->delete($db->quoteName('#__radenium_ffmpeg'));
        $query->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $results = $db->loadObjectList();

        return $results;
    }

        
	public function publishLive( $id ) {
		
		//$pid = exec("python ".$comand.$this->noterminal, $out);		
		//echo $pid;
		
		exec("python components/com_radenium/models/python/phppublishremote.py -id ".$id." 2>&1", $out);
		//print_r($out);
		
		return $out;
	}
	
	
	public function startTake( $id, $data, $devices ) {
		$noterminal = " </dev/null >/dev/null 2>ffmpeg.log & echo $!";
		$ffmpeg = "/usr/local/bin/ffmpeg";
		$vid_url = getcwd()."/media/com_radenium/media/takes/id_".$id;
		mkdir($vid_url, 0757);
		mkdir($vid_url."/thumbs", 0757);
		
		//mkdir($vid_url."_copy", 0757);

		$devstr="";
		// Do we have to start audio as well?
		if ( $devices["audio"]["sysid"] == "" ) {
			$devstr = $devices["video"]["sysid"];
		} else {
			$devstr = $devices["video"]["sysid"].":".$devices["audio"]["sysid"];
		}
		
		$command = $this->getEntry($data['format'], $where=array("category"=>"takes"))[0];
		
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
		$res = JFormHelper::loadFieldType('ScreenResolution', false);
		$res = $res->getOptions(); // works only if you set your field getOptions on public!!
		
		
		$ffmpegcom= str_replace("{\$DEVICES}", $devstr, $command["command"]);
		$ffmpegcom= str_replace("{\$OUT_DIR}", $vid_url."/", $ffmpegcom);
		$ffmpegcom= str_replace("{\$OUT_NAME_M3U8}", "playlist.m3u8", $ffmpegcom);
		
		if ( strpos(strtolower($res[$data["resolution"]]), "screen") === False ) {
			$resolution = explode(" ", $res[$data["resolution"]]);
			$resolution = $resolution[1];
			
		} else {
			$resolution = "640x380";
			
		}
		
		$ffmpegcom = str_replace("{\$OUT_RES}", $resolution, $ffmpegcom);
		
		//Rtsp settings
		$ffmpegcom = str_replace("{\$RTSP_USER_NAME}", $data["settings"]["rtsp_user"], $ffmpegcom);
		$ffmpegcom = str_replace("{\$RTSP_USER_PASSWORD}", $data["settings"]["rtsp_password"], $ffmpegcom);
		$ffmpegcom = str_replace("{\$RTSP_SERVER_URL}", $data["settings"]["rtsp_url"], $ffmpegcom);
		$ffmpegcom = str_replace("{\$RTSP_SERVER_PORT}", $data["settings"]["rtsp_port"], $ffmpegcom);
		$ffmpegcom = str_replace("{\$RTSP_KEY}", $data["settings"]["rtsp_key"], $ffmpegcom);
		//$ffmpegcom .= " copy ".$vid_url."_copy/copy.mp4";
		/*
		
		print_r($ffmpegcom);
		
		echo "<pre>";
		
		echo "<br />";
		print_r($data["settings"]);
		echo "</pre>";
		
		
		die;
		*/
		
		
		//$ffmpegcom = "-r 30 -f avfoundation -i ".$devstr." -pix_fmt yuv420p -s 640X320 -hls_flags round_durations -hls_time 3 -hls_init_time 3 /Applications/MAMP/htdocs/radenium/media/com_radenium/media/takes/id_".$id."/playlist.m3u8";
		//$ffmpegcom = "-r 30 -f avfoundation -i 0:0 -pix_fmt yuv420p -s 640X320 -hls_flags round_durations -hls_time 3 -hls_init_time 3 /Applications/MAMP/htdocs/radenium/media/com_radenium/media/takes/id_".$id."/playlist.m3u8";
		
		ini_set('max_execution_time', 0);
		
		//echo $ffmpeg." ".$ffmpegcom.$noterminal;
		
		$pid = exec($ffmpeg." ".$ffmpegcom.$noterminal, $out);
		
		return $pid;
	}
	
	public function getFrameRates( $dev ) {
		exec("/usr/local/bin/ffmpeg -r 1 -f avfoundation -i ".$dev." 2>&1", $out);
		foreach( $out as $l ) {
			// That's a line containing a framerate.
			
		}
	}
	
	
	/**
	 * @todo Thumbs are created from .ts files, however, they are segmented so when a time is given I should
	 *  find out which .ts file to pick.
	 * @param unknown $dir
	 * @param unknown $file
	 * @param string $time
	 */
	public function getThumbNails( $dir, $file, $time="00:00:01.000" ){
		$time="00:00:9.000";
		$tardir = getcwd()."/".$dir;

		exec("/usr/local/bin/ffmpeg -ss 3 -i ".$tardir.$file." -vf \"select=gt(scene\,0.4)\" -frames:v 5 -vsync vfr -vf fps=fps=1/600 ".$tardir."thumbs/out%02d.jpg 2>&1", $out);
		//ffmpeg -ss 3 -i input.mp4 -vf "select=gt(scene\,0.4)" -frames:v 5 -vsync vfr -vf fps=fps=1/600 out%02d.jpg
		
		/*
		echo "<pre>";
		print_r($out);
		echo "</pre>";
		 */
		
		$ffmpeg_com="/usr/local/bin/ffmpeg -i ".$tardir.$file." -ss ".$time." -frames:v 1 ".$tardir."thumbs/thumb.jpg";
		
		//echo "<p>".$ffmpeg_com."</p>";
		
		exec($ffmpeg_com." 2>&1", $out);

	}
	
	
	public function stopTake( $pid ) {
		if ( $pid > 0 ) {
			$pid = exec("kill ".$pid, $out);
		}
		
		return true;
	}
	
	private function parseSystemDeviceOutputLine( $line ) {
		$device = array();
		$d = explode("] [", $line);
		$d = explode("] ", $d[1]);
		$device["sysid"] = $d[0];
		$device["name"] = $d[1];
		$device["idstr"] = $device["sysid"]."_".strtolower(str_replace(" ", "_", $device["name"]));

		return $device;
	}
	
    public function getSystemDevices() {
        //ini_set('max_execution_time', 0);
        $noterminal = " </dev/null >/dev/null 2>python.log & echo $!";
        $noterminal = " </dev/null >/dev/null 2>python.log &";
        //$result = exec("python components/com_radenium/models/python/getsystemdevices.py".$noterminal, $out);
        //$result = exec("/usr/local/bin/python3.6 components/com_radenium/models/python/getsystemdevices.py".$noterminal, $out);

//"/usr/local/bin/ffmpeg", "-f", "avfoundation", "-list_devices", "true", "-i", ""
        //$result = shell_exec("/usr/local/bin/ffmpeg -f avfoundation -list_devices true -i \"\"");

        exec("/usr/local/bin/ffmpeg -f avfoundation -list_devices true -i \"\" 2>&1", $out);
		$devs = array();
		$parsevideodevicesnow = false;
		$parseaudiodevicesnow = false;
        
        foreach( $out as $l ) {
            if (strpos($l, ":") !== false){ // Otherwise it parses the ': input output error' as well.
            	if (strpos($l, "video")) {
               		$parsevideodevicesnow = true;
               		$parseaudiodevicesnow = false; // you never know...
               		
            	} else if ( strpos($l, "audio")){
	            	$parseaudiodevicesnow = true;
	            	$parsevideodevicesnow = false;
	            	
            	}
            } else if ($parsevideodevicesnow) {
            	$devs['video'][] = $this->parseSystemDeviceOutputLine($l);
            	
            } else if ($parseaudiodevicesnow) {
            	$devs['audio'][] = $this->parseSystemDeviceOutputLine($l);
            	
            }
        }
        
        $devs['audio'][] = array("sysid"=>"", "name"=>"No Audio", "idstr"=>"no_audio");
        $devs['video'][] = array("sysid"=>"", "name"=>"No Video", "idstr"=>"no_video");
        
/*
$output = shell_exec('ffprobe -v quiet -print_format json -show_format -show_streams "path/to/yourfile.ext"');
$parsed = json_decode($output, true);
*/
        return $devs;
        
    }

}
