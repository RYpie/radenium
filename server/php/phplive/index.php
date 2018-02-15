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

}

$site = new radlive();
$channels = $site->ls("channels");
$live = $site->ls("live");

echo "<pre>";
print_r( $channels );
print_r( $live );
echo "</pre>";

?>

<html>
    <head>
        <title>Radenium Live</title>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body>

Hi there

    </body>
</html>

