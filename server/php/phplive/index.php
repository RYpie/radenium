<?php

class radlive {
    public function ls($dir) {
        $retVal = array();
        $list = scandir($dir);
        foreach ($list as $l) {
            if (
            		($l != ".") 
            		&& ($l != "..") 
            		&& ( $l != ".DS_Store")
            		&& ( $l != "index.html")
            		
            	) {
                $retVal[] = $l;
                
            }
            
        }
        return $retVal;
        
    }
    
    public function livenow() {
    	
    	
    }

}

$site = new radlive();
$channels = $site->ls("channels");
$live = $site->ls("live");

$channels_info = array();

//should check for m3u8 file
foreach ( $channels as $ch ) {
	$channels_info[$ch] = array();
	$channels_info[$ch]["live"] = false;
	foreach ( $live as $l ) { 
		if ( $l == $ch ) {
			$channels_info[$ch]["live"] = true;
		}
	}
	
}

echo "<pre>";
print_r( $channels );
print_r( $live );
print_r($channels_info);
echo "</pre>";

?>

<html>
    <head>
        <title>Radenium Live</title>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body>

<?php 
foreach ( $channels_info as $key => $ch ){
	echo "<div class=\"channel_info_badge\">";
	echo "<h3>".$key."</h3>";
	if ($ch["live"]) {
		echo "<p>Live now!</p>";
	}
	echo "</div>";
}
?>

    </body>
</html>

