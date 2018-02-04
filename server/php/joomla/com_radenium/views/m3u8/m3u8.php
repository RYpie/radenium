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


class m3u8
{
	private $version;
	private $mediadir="media/com_radenium/media/takes/";
	private $m3u8_src_f = 'playlist.m3u8';
	private $m3u8_tar_f = 'take.m3u8';
	public $m3u8_src;
	
	
	
	public function __construct($id=false) {
		if ($id) {
			//Check if the m3u8 exists.
			//if not, create a fake one that plays an existing clip
			// the moment the playlist.m3u8 exist, begin to copy lines into video.m3u8
			$this->m3u8_src=$this->getSrcM3u8($id);

			return $this->m3u8_src;
			
		} else {
			return false;
			
		}
	}
	
	
	private function getSrcM3u8($id) {
		$media=array();
		//Get a list of all the media files:
		$this->getDirContents($this->mediadir."id_".$id, $media, $findfile ="playlist.m3u8");
		
		//print_r($media);

		if ( count($media) > 0 ) {
			//echo "playlist ready...";
			
			$read = file_get_contents($media[0]);
			$lines= explode("\n",$read);
			$new_m3u8 = "";
			
			//echo "<pre>";
			$addit = False;
			foreach( $lines as $l ){
				//print_r($l."\n");
				
				if ( !$addit ) {
					$new_m3u8 .= $l;
					
				} else {
					//echo "adding directory\n";
					$new_m3u8 .= "media/com_radenium/media/takes/id_".$id."/".$l;
					$addit = false;
					
				}
				$new_m3u8 .= "\n";
				
				//echo "testing line ". $l."\n";
				if ( strpos($l, "#EXTINF:") !== false ) {
					//echo "\nnext line is one.\n";
					$addit = true;
					
				}
			}
			/*
			echo "<hr />";
			echo $new_m3u8;
			echo "</pre>";
			*/
			
			//die;
			return $new_m3u8;
			
			
		} else {
			//echo "create a fake list...";
			
			return "#EXTM3U".PHP_EOL.
"#EXT-X-VERSION:3".PHP_EOL.
"#EXT-X-TARGETDURATION:9".PHP_EOL.
"#EXT-X-MEDIA-SEQUENCE:1".PHP_EOL.
"#EXTINF:1,".PHP_EOL.
"media/com_radenium/media/takes/startup/playlist1.ts".PHP_EOL. 
"#EXTINF:2,".PHP_EOL.
"media/com_radenium/media/takes/startup/playlist2.ts".PHP_EOL.
"#EXTINF:3,".PHP_EOL.
"media/com_radenium/media/takes/startup/playlist3.ts".PHP_EOL.
"#EXTINF:4,".PHP_EOL.
"media/com_radenium/media/takes/startup/playlist4.ts".PHP_EOL.
"#EXTINF:5,".PHP_EOL.
"media/com_radenium/media/takes/startup/playlist5.ts".PHP_EOL;

			
		}
	}
	
	
	private function getDir($dir, &$results = array(), $finddir = False){
		if(substr($dir, -1) == '/') {
			$dir = substr($dir, 0, -1);
		}
		$files = scandir($dir);
		foreach($files as $key => $value){
			$path = $dir.DIRECTORY_SEPARATOR.$value;//realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				
			} else if($value != "." && $value != ".." && $value != ".DS_Store") {
				$this->getDir($path, $results, $finddir);
				if ( $finddir != False ){
					if ( $value == $finddir ){
						$results[] = $path;
					}
				}
			}
		}
		
		return $results;
	}
	
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
}

