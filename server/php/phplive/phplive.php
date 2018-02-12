<?php

include_once('configuration.php');

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

class HLSUpdater {
	private $streamdirectory;
	private $db=False; // Object to a database
	
	function __construct( $targetdir, $db = False ) {
		$this->db = $db;
		if ( substr($targetdir, -1) != '/' ){
			$targetdir = $targetdir.'/';
		}
	
		$this->streamdirectory = $targetdir;
		$this->CreateDir($this->streamdirectory);
	}
	
	/**
	 * @desc Creates a directory if it does not exist.
	 * @param string $dir
	 */
	function CreateDir( $dir ) {
		if ( !file_exists($dir) ) {
			mkdir($dir, 0755);
		}
	}
	
	function Update() {
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
	public function __construct( $url ) {
		
	}
	
}


/**
 * @desc Cleanses and swipes a complete target directory
 * @param string $dir Target directory to be cleansed
 */
function MakeOrCleanDirectory($dir) {
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

function printp($p) {
    print_r( "\n\n<p>".$p."</p>\n\n" );
    
}

$rconf = new RConfig();
//printp($rconf->stream_key);
$radeniumvalidUser = True;
$hls = new HLSUpdater( getcwd().'/live/'.$_REQUEST['RADENIUM_API_UNAME']."/" );
$ical = new icalParser($rconf->ical);

if ( $radeniumvalidUser ) {
    $target_dir = getcwd().'/live/'.$_REQUEST['RADENIUM_API_UNAME']."/";
    $retVal = array();
    
    switch ( $_REQUEST['task']) {
    case 'announce_live':
        MakeOrCleanDirectory($target_dir);
        break;
        
    case 'hls_update':
    	$hls->Update();
        array_push($retVal, array('hls_update' => "ok"));
        break;
    
    case 'clean_up_stream':
        MakeOrCleanDirectory($target_dir);
        break;
        
    case 'live_events':
        array_push( $retVal, array('live_events' => "no events" ) );
        break;
    
    default:
        break;
    }

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
