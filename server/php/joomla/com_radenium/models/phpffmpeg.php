<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class RadeniumModelPhpffmpeg extends JModelItem
{
	/**
	 * @var string message
	 */
	protected $message;

	/**
	 * Get the message
         *
	 * @return  string  The message to be displayed to the user
	 */
	public function getMsg()
	{
		if (!isset($this->message))
		{
			$this->message = 'Hello World!';
		}

		return $this->message;
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

        
/*
$output = shell_exec('ffprobe -v quiet -print_format json -show_format -show_streams "path/to/yourfile.ext"');
$parsed = json_decode($output, true);
*/
        return $devs;
        
    }
	
}