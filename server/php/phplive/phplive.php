<?php

include_once('configuration.php');

/**
* @desc Parses m3u8 files in a manner relevant to HLS update/post functionality.
*/
class m3u8Parser {
	private $playlist_txt;
	private $playlist_pieces;
	
	public function __construct( $playlist_txt ) {
		$this->playlist_txt = $playlist_txt;
		$this->parse();
	}
	
	public function parts() {
		return $this->playlist_pieces;
	}
	
	/**
	 * @desc parses a m3u8 playlist file.
	 */
	private function parse() {
		$pieces = explode("\n", $this->playlist_txt); // make an array out of curl return value
		$pieces = array_map('trim', $pieces); // remove unnecessary space
		$i = 0;
		$file_descriptor = 0;
		foreach ($pieces as $line) {
			if (substr_count($line, '#EXTINF') ) {
				$this->playlist_pieces[$file_descriptor] = array( '#EXTINF'=>$line,'file'=> $pieces[$i+1]);
				$file_descriptor++;
				
			}
			$i++;
			
		}		
	}
}

/**
* @desc Class used to update a HLS stream on the server. 
* 	To get the best out of this class, it requires to meet certain upload order requests, namely, first upload .ts files, and finally the .m3u8.
* 	That is to make sure that the .ts files are available when the updated files are available.
* @todo Make the class smarter so that it does not matter when which file is uploaded.
* 	Database connection to register data upload sizes for traffic calculations.
*/
class HLS {
	private $streamdirectory; 	/*!< Directory in which the HLS stream will be stored. */
	private $db=False; 			/*!< Database object for future use, currently not defined. */
	
	/**
	* @desc Constructor.
	* @param string $targetdir Location where to store the HLS stream.
	* @param string $task Depending on the task it performs different actions.
	* @param object $db Database object, for future use, currently not defined.
	*/
	function __construct( $targetdir, $task, $db = False ) {
		$this->db = $db;
		if ( substr($targetdir, -1) != '/' ){
			$targetdir = $targetdir.'/';
			
		}
	
		$this->streamdirectory = $targetdir;
		
	}
	
	/**
	 * @todo Perhaps use session variable to keep track of the state, so was it announced etcetera and more of that stuff.
	 * 
	 * @param unknown $task
	 * @return array
	 */
	function hls_execute($task) {
		$retVal = array();
		switch ( $task ) {
			case "announce_live_stop":
			case "announce_live_start":
				$this->prepareStreamingDirectory();
				array_push($retVal, array($task => "ok"));
				break;
				
			case 'hls_update':
				$this->update();
				array_push($retVal, array($task => "ok"));
				break;
				
			case 'clean_up_stream':
				$this->prepareStreamingDirectory();
				array_push($retVal, array($task => "ok"));
				break;
				
			/**
			 * @todo what is this thing doing here?
			 */
			case 'live_events':
				array_push( $retVal, array('live_events' => "no events" ) );
				break;
				
			default:
				break;
				
		}
		
		return $retVal;
	}
	
	private function prepareStreamingDirectory() {
		$this->MakeOrCleanDirectory($this->streamdirectory);
		
	}
	
	/**
	 * @desc Creates a directory if it does not exist.
	 * @param string $dir Directory to be created.
	 */
	function CreateDir( $dir ) {
		if ( !file_exists($dir) ) {
			mkdir($dir, 0755);
			
		}
		
	}
	
	/**
	* @desc Updates a HLS stream via .m3u8 file. It first receives the .ts files and finally gets the .m3u8 file in order to delete old files.
	*/
	function update() {
		$totalSizeInFiles = 0;
		foreach ($_FILES as $file ){
			$filename = $this->streamdirectory.$file['name'];
			move_uploaded_file( $file['tmp_name'], $filename );
			@chmod( $filename, 0755 );
			$totalSizeInFiles += filesize( $filename );
			
			// Check if the this is an .m3u8 file.
			$fileparts = explode('.',$file['name']);
			if ( $fileparts[1] == 'm3u8' )
			{
				$handle = fopen($filename, "r");
				$m3u8_txt = fread($handle, filesize($filename));
				$m3u8 = new m3u8Parser( $m3u8_txt );
				$keepfiles = array();
				foreach ( $m3u8->parts() as $f ){
					array_push($keepfiles, $f['file']);
					
				}
				array_push($keepfiles, "playlist.m3u8");				
				$this->KeepFiles( $keepfiles );
				
			}
			
		}
		if ( $totalSizeInFiles> 0 ) {
			
			$this->DBUpdate_TrafficByUploadedFiles( $this->db, $uploadSize, 0);
		}
		
		return $totalSizeInFiles;
		
	}
	
	/**
	* @desc Function that deletes files from a directory that are not in the $keepfiles list.
	* @param array $keepfiles Array that holds the names of files in the streaming directory that should not be deleted.
	*/
	function KeepFiles( $keepfiles ) {
		$dirfiles = glob( $this->streamdirectory.'*' );
		$pop = explode('/', $this->streamdirectory);
		foreach( $dirfiles as $f){
			$fname = end(explode('/',$f));
			$found = false;
			foreach ($keepfiles as $kf) {
				if ($fname == $kf ){
					$found = true;
					
				}
				
			}
			if ( !$found ) {
				printp( '\nDeleting:'.$f);
				if(is_file($f)){
					unlink($f); // delete file
					
				}
				
			}
			else {
				// Not deleting file, pass
				
			}
			
		}
		
	}
	
	/**
	 * @desc Prepares an empty directory having name $dir. If it exists, its contents will be deleted.
	 * @param string $dir Target directory to be cleansed
	 */
	private function MakeOrCleanDirectory($dir) {
		if ( !file_exists($dir) ) {
			mkdir($dir, 0755);
			
		} else {
			$files = glob($dir.'*'); // get all file names
			foreach($files as $file){ // iterate files
				if(is_file($file)){
					unlink($file); // delete file
					
				}
				
			}
			
		}
		
	}
	
	/**
	 * @todo This function is currently not storing anything.
	 * 
	 * @desc Updated the database with traffic.
	 * @param object $db Database object where to update
	 * @param int $fSize Size of the file that has been uploaded now.
	 * @param int $user The user who uploaded this file. (\todo Currently not handled)
	 */
	function DBUpdate_TrafficByUploadedFiles($db=False, $fSize, $user) {
		if ( $user == "" ) {
			$user = 0;
			
		}
		$cols = "";
		$vals = "";
		$cols = array("uploaded_data", "rad_user", "date_stamp");
		$vals = array($fSize, 3, "CURRENT_DATE");
		
		//get current date data
		if ( $result = $db->getAllTableDataWhere( "radenium_stats", "date_stamp=\"2017-01-05\"" ) ) {
			$fSize = intval($fSize) + intval( $result[0]->uploaded_data );
			$cols = array("uploaded_data", "rad_user");
			$vals = array($fSize, 3);
			if ( !$db ) {
				$db->updateTable( "radenium_stats", $cols, $vals, "`id`=".$result[0]->id );
				
			}
			
		} else {
			if ( !$db ) {
				$db->insertTable( "stats", $cols, $vals, "" );
				
			}
			
		}
		
	}
	
}

class icalParser {
	/**
	 * @desc Should return events created on livestreaming server.
	 *  Not sure yet, where to administer that stuff. Maybe on the livestreaming server.
	 *  Otherwise you can create them on the radstudio.
	 *  should return the ical url to be called by python.
	 * @param unknown $url
	 */
	public function __construct( $url ) {
		
	}
	
}




function printp($p) {
    print_r( "\n\n<p>".$p."</p>\n\n" );
    
}

$rconf = new RConfig();
//printp($rconf->stream_key);
$radeniumvalidUser = True;
$hls = new HLS( getcwd().'/live/'.$_REQUEST['uname']."/" );
$ical = new icalParser($rconf->ical);

if ( $radeniumvalidUser ) {
    $retVal = $hls->hls_execute($_REQUEST['task']);
    
	if ( $validDebugUser ) {
        echo '\n>>>REQUEST VARIABLES:<<<\n';
        print_r( $_REQUEST );

    } else {
        echo json_encode( $retVal );
        
    }
    
} else {
    echo json_encode( array("API OK") );
    
}

?>
