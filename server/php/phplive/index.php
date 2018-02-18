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
foreach ( $live as $l ) {
	//if ( $l == $ch ) {
	//echo $l."<br />";
	if ( file_exists( "live/".$l."/playlist.m3u8" ) ) {
		$channels_info[$l]["live"] = true;
		
	}
}




?>

<html>
    <head>
        <title>Radenium Live</title>
		<script src="js/inourplace.1.0.0.js" ></script>
		<link href="css/inourplace.1.0.0.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body>
    
<div id="container">

<h1>Radenium Live Streaming</h1>
<div id="nav_height_keeper">
	<div id="nav" class="nav">
		<div id="videoplay_bar">
			<div>
				<div id="videoplayercontainer"></div>
				
			
			
			<div id="videoplayercontainer_right_column">
				<div style="text-align:right;">
					<div 
						id="videoplayercontainer_close" 
						onclick="hideVideoPlayBar()">
						Close
					</div>
				</div>
				<div id="videoplayercontainer_content"></div>
			
				<div>
					Subscribe to our newsletter<br />
					<input type="text" value="" name="email" /><input type="submit" name="submit" value="Subscribe!" />
				</div>
			</div>
			
			</div>
			
		</div>
		
		<div class="clr"></div>
	</div>
</div>

<div class="clr"></div>


<h1>Live Now!</h1>
<?php 
foreach ( $channels_info as $key => $ch ){
	echo "<div class=\"channel_info_badge\" ";
	echo "onclick=\"playVideo('live/revolution/playlist.m3u8', 460, 320, 'Nice video','Live now')\" ";
	echo ">";
	echo "<h3>".$key."</h3>";
	if ($ch["live"]) {
		echo "<p>Live now!</p>";
	}
	echo "</div>";
}
?>
	<div class="clr"></div>
	</div>
    </body>
</html>

<?php 

echo "<pre>";
print_r( $channels );
print_r( $live );
print_r($channels_info);
echo "</pre>";
echo "<pre>";
print_r( $channels );
print_r( $live );
print_r($channels_info);
echo "</pre>";
echo "<pre>";
print_r( $channels );
print_r( $live );
print_r($channels_info);
echo "</pre>";
echo "<pre>";
print_r( $channels );
print_r( $live );
print_r($channels_info);
echo "</pre>";
echo "<pre>";
print_r( $channels );
print_r( $live );
print_r($channels_info);
echo "</pre>";

?>

<br />