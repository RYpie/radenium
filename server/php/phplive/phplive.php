<?php
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
	
	function __construct( $targetdir ) {
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
			//echo "Updating yEeeeeeees: ". $filename;
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
					//print_r($f);
					array_push($keepfiles, $f['file']);
				}
				array_push($keepfiles, "playlist.m3u8");
				//print_r( $m3u8->parts() );
				
				$this->KeepFiles( $keepfiles );
			}
			
		}
		
		return $totalSizeInFiles;
	}
	
	function KeepFiles( $keepfiles ) {
		$dirfiles = glob( $this->streamdirectory.'*' );
		$pop = explode('/', $this->streamdirectory);
		
		//echo '<p>Trying to delete</p>';
		foreach( $dirfiles as $f){
			$fname = end(explode('/',$f));
			printp($fname);
			$found = false;
			foreach ($keepfiles as $kf){
				if ($fname == $kf ){
					echo "<h2>Found one!</h2>";
					$found = true;
				}
			}
			if ( !$found ){
				printp( '\nDeleting:'.$f);
				if(is_file($f)){
					unlink($f); // delete file
					
				}
			}
			else{
				echo "Not deleting: ".$f;
				
			}
		}
		
	}
}

    
    /*!
    \param $db Database where to update
    \param $fSize Size of the file that has been uploaded now.
    \param $user The user who uploaded this file. (\todo Currently not handled)
    */
    function DBUpdate_TrafficByUploadedFiles($db=False, $fSize, $user)
    {
        if ( $user == "" ) {
            $user = 0;
        }
        $cols = "";
        $vals = "";
        $cols = array("uploaded_data", "rad_user", "date_stamp");
        $vals = array($fSize, 3, "CURRENT_DATE");
        
        //get current date data
        if ( $result = $db->getAllTableDataWhere( "radenium_stats", "date_stamp=\"2017-01-05\"" ) ) {
            echo "<h1>UPdate</h1>";
            print_r( $result );
            $fSize = intval($fSize) + intval( $result[0]->uploaded_data );
            echo "<h2>".$fSize."</h2>";
            $cols = array("uploaded_data", "rad_user");
            $vals = array($fSize, 3);
            if ( !$db ) {
            	$db->updateTable( "radenium_stats", $cols, $vals, "`id`=".$result[0]->id );
            }
        } else {
            echo "<h1>New this day</h1>";
            if ( !$db ) {
            	$db->insertTable( "stats", $cols, $vals, "" );
            }
        }
    }

    /*!
    \brief Cleanses and swipes a complete target directory
    \param $dir Target directory to be cleansed
    */
    function MakeOrCleanDirectory($dir)
    {
        echo "Cleaning up directory: ".$dir;
        if ( !file_exists($dir) )
        {
            mkdir($dir, 0755);
            echo "Directory did not exists...";
        }
        else
        {
            $files = glob($dir.'*'); // get all file names
            print_r($files);
            foreach($files as $file){ // iterate files
                if(is_file($file)){
                    unlink($file); // delete file
                }
            }
        }
    }
    
    function IfDirNotExistCreateIt( $dir )
    {
        if ( !file_exists($dir) )
        {
            mkdir($dir, 0755);
            echo "Directory did not exists, creating: ". $dir;
        }
    }
    
    /*!
    \brief Deletes all the files in a directory that are not in the $keepfiles list.
    \param $dir Directory in which deletion has to take place
    \param $keepfiles list of files to keep
    \param $savedir A directory which at least should be part of $dir, to prevent that the sytem is wiped by accident...
    */
    function KeepFiles( $dir, $keepfiles )
    {
        if ( substr($dir, -1) != '/' ){
            $dir = $dir.'/';
        }
        
    
        $dirfiles = glob($dir.'*');
    
        $pop = explode('/', $dir);

        //echo '<p>Trying to delete</p>';
        foreach( $dirfiles as $f){
            $fname = end(explode('/',$f));
            printp($fname);
            $found = false;
            foreach ($keepfiles as $kf){
                if ($fname == $kf ){
                    echo "<h2>Found one!</h2>";
                    $found = true;
                }
            }
            if ( !$found ){
                printp( '\nDeleting:'.$f);
                if(is_file($f)){
                    unlink($f); // delete file
                }
            }
            else{
                echo "Not deleting: ".$f;
            }
        }

    }


    /*!
    \brief Updates the stream files with new .ts files and new .m3u8 files. Upon a new .m3u8 file all the files that are not part of the m3u8 file are deleted to keep required server space low.
    \param $streamdirectory Location where the stream to be updated is being published.
    */
    function UpdateStreamFiles( $streamdirectory )
    {
        $totalSizeInFiles = 0;

        /*! Check if the directory exists, if not an error might have occured during announcing. 
        */
        
        //MakeOrCleanDirectory
        foreach ($_FILES as $file ){
            $filename = $streamdirectory.$file['name'];
            
            echo "\n\nMoving ".$file['tmp_name']." to: ".$filename."/n/n";
            
            echo move_uploaded_file( $file['tmp_name'],$streamdirectory.$file['name']);
            
            @chmod($streamdirectory.$file['name'], 0755);
            echo "\n<h2>is now uploaded\n</h2>\n";
            $totalSizeInFiles += filesize( $streamdirectory.$file['name'] );
            // Check if the this is an .m3u8 file.
            $fileparts = explode('.',$file['name']);
            //print_r( $fileparts );
            if ( $fileparts[1] == 'm3u8' )
            {
                $handle = fopen($filename, "r");
                $m3u8_txt = fread($handle, filesize($filename));
                $m3u8 = new m3u8Parser( $m3u8_txt );
                $keepfiles = array();
                foreach ( $m3u8->parts() as $f ){
                    //print_r($f);
                    array_push($keepfiles, $f['file']);
                }
                array_push($keepfiles, "playlist.m3u8");
                //print_r( $m3u8->parts() );
                
                KeepFiles( $streamdirectory, $keepfiles );
            }
        
        }
        
        return $totalSizeInFiles;
    }
    
    function printp($p)
    {
        echo '<p>'.$p.'</p>';
    }



$radeniumvalidUser = True;
$hls = new HLSUpdater( getcwd().'/live/'.$_REQUEST['RADENIUM_API_UNAME']."/" );

//DBUpdate_TrafficByUploadedFiles( $db, 3000, "0" );


if ( $radeniumvalidUser )

{
    $target_dir = getcwd().'/live/'.$_REQUEST['RADENIUM_API_UNAME']."/";
    
    $retVal = array();
    
    switch ( $_REQUEST['task'])
    {
    case 'announce_live':
        MakeOrCleanDirectory($target_dir);
        break;
    case 'hls_update':
        //$uploadSize = UpdateStreamFiles($target_dir);
        $hls->Update();
        //Updating the dataBase with the traffic of the filesize.            
        if ( $uploadSize > 0 )
        {
        	$db = False;
            DBUpdate_TrafficByUploadedFiles( $db, $uploadSize, 0);
        }
        array_push($retVal, array('live_settings' => "no settings available"));
        break;
    
    case 'clean_up_stream':
        MakeOrCleanDirectory($target_dir);
        break;
        
    case 'heart_beat':
        //array_push( $retVal, array('live_events' => $radenium ->getRadeniumLiveEvents() ) );
        array_push( $retVal, array('live_events' => "no events available" ) );
        
        break;
    
    default:
        break;
    }


	if ( $validDebugUser ) {
        echo '\n>>>REQUEST VARIABLES:<<<\n';
        print_r( $_REQUEST );

    }
    else
    {
        echo json_encode( $retVal );
    }
}
else
{
    echo json_encode( array("API OK") );
}
?>