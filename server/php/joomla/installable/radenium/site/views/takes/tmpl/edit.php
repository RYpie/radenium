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

$vidurl = "media/com_radenium/media/takes/id_".$this->entry_data[0]->id."/video.m3u8";
$videom3u8 = "media/com_radenium/media/takes/id_".$this->entry_data[0]->id."/video.m3u8";

if ( !file_exists($vidurl) ) {
	$vidurl = "media/com_radenium/media/takes/id_".$this->entry_data[0]->id."/playlist.m3u8";
	
}


$posterurl = "media/com_radenium/media/takes/id_".$this->entry_data[0]->id."/thumbs/thumb.jpg";


//$vidurl = "index.php?option=com_radenium&view=m3u8&format=raw&take_id=".$this->entry_data[0]->id;
//$m3u8_status = "index.php?option=com_radenium&view=m3u8&task=getstatus&format=raw&take_id=".$this->entry_data[0]->id;
//$m3u8_status = "index.php?option=com_radenium&view=takes&task=m3u8status&format=raw&take_id=".$this->entry_data[0]->id;
//$vidurl = "index.php?option=com_radenium&view=m3u8&format=raw&take_id=".$this->entry_data[0]->id;


$xml = $this->form->getXml();
foreach ( $xml->fieldset as $f ) {
	if ( strtolower( (string)$f->attributes()->name) == "hidden" ) {
		foreach( $f->field as $e ){
			if ((string)$e->attributes()->name != "user_id")
			{
				$this->form->setValue( (string)$e->attributes()->name, null, JFactory::getApplication()->input->get( (string)$e->attributes()->name ) );				
			
			}
		
		}
		
	} else {
		
	}
	
}


//Get the custom field for visualisation of the selected data.
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
$vdevs = JFormHelper::loadFieldType('Videodevices', false);
$vdevs = $vdevs->getOptions(); // works only if you set your field getOptions on public!!
$adevs = JFormHelper::loadFieldType('Audiodevices', false);
$adevs = $adevs->getOptions(); // works only if you set your field getOptions on public!!
$res = JFormHelper::loadFieldType('ScreenResolution', false);
$res = $res->getOptions(); // works only if you set your field getOptions on public!!
$format = JFormHelper::loadFieldType('ffmpeg', false);
$format= $format->getOptions(); // works only if you set your field getOptions on public!!


$info = array(
		"Video Input Device"=>$vdevs[$this->entry_data[0]->vid]
		, "Audio Input Device"=>$adevs[$this->entry_data[0]->aid]
		, "Screen Resolution" => $res[$this->entry_data[0]->resolution]
		, "Recording Format" => $format[$this->entry_data[0]->format]
		, "Take date" => $this->entry_data[0]->takedate
		, "Process ID" => $this->entry_data[0]->pid
		, "Take ID" => $this->entry_data[0]->id
);

foreach( $this->entry_data[0] as $key => $val ) {
	$this->form->setValue( $key, null, $val);
	
}
//print_R($this->entry_data[0]->id);
/*
echo "<pre>";
print_r($this->entry_data[0]);
echo "</pre>";
*/

?>

<script type="text/javascript">
var jQueryRepresentatives = jQuery.noConflict();

jQueryRepresentatives(document.body).on('click','#button_stoptake', function(){
	jQueryRepresentatives.ajax({
		type: 'GET',
		url: "index.php?option=com_radenium&amp;view=takes&amp;format=raw&amp;task=stoptake&amp;takes_id=<?php echo $this->entry_data[0]->id; ?>",
		success:function(data){
			//jQueryRepresentatives('#results').html(data);
		},
		error:function(){
			//jQueryRepresentatives('#results').html('<p class="error">An error was encountered while retrieving the representatives from the database.</p>');

		}

	});

	checkForFullPlaylist();
	
});

jQueryRepresentatives(document.body).on('click','#button_togglelive', function(){
	info = ["Stopped publishing", "Publishing Right Now!"];
	// Example url:
	// http://localhost:8888/radenium/index.php?option=com_radenium&amp;view=takes&amp;format=raw&amp;task=publishlive&amp;takes_id=188
	jQueryRepresentatives.ajax({
		type: 'GET',
		url: "http://localhost:8888/radenium/index.php?option=com_radenium&amp;view=takes&amp;format=raw&amp;task=publishlive&amp;takes_id=<?php echo $this->entry_data[0]->id; ?>",
		success:function(jdata){
			data = JSON.parse(jdata);

			jQueryRepresentatives('#publishlive_info').html(info[data['publish']]);
			
			jQueryRepresentatives('#button_togglelive').html("Live Now!");
		},
		error:function(){
			//jQueryRepresentatives('#results').html('<p class="error">An error was encountered while retrieving the representatives from the database.</p>');

		}
		
	});
	
});

jQueryRepresentatives(document.body).on('click','#create_thumbs', function(){
	var vid = document.getElementById("myVideo");
	//vid.currentTime = 5;
	alert(vid.currentTime);
	jQueryRepresentatives.ajax({
		type: 'GET',
		url: "http://localhost:8888/radenium/index.php?option=com_radenium&amp;view=ffmpeg&amp;format=raw&amp;task=createthumbs&amp;takes_id=<?php echo $this->entry_data[0]->id; ?>&amp;player_position="+vid.currentTime,
		success:function(data){
			//jQueryRepresentatives('#results').html(data);
			//alert(data);
			//jQueryRepresentatives('#button_togglelive').html("Live Now!");
			
		},
		error:function(){
			//jQueryRepresentatives('#results').html('<p class="error">An error was encountered while retrieving the representatives from the database.</p>');

		}
		
	});
	
});
</script>

<script type="text/javascript">
function checkForFullPlaylist() {
    var http = new XMLHttpRequest();
    var source = document.getElementById("myVideoSrc");
    var vid = document.getElementById("myVideo");
    
    http.open('HEAD', "<?php echo $videom3u8;?>", false);
    http.send();

	if(http.status != 404 ) {
		vid.pause();
		source.setAttribute('src', '<?php echo $videom3u8;?>');
		vid.load();
		vid.currentTime = 0;
		vid.play();
		
	} else {
		setTimeout( checkForFullPlaylist, 3000 );

	}
	
}
</script>

<form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_takes" name="radenium_takes">

	<div id="player_area">
		<div style="float:left;">
		    <div>
                <video id="myVideo" controls autoplay="1" width="600px" height="360px" poster="<?php echo $posterurl; ?>">
                    <source id="myVideoSrc" src="<?php echo $vidurl; ?>" type="video/mp4">
                </video>
			</div>
			<div style="width:600px;" class="form_rendered_container_form_">
			<h2>Create clip</h2>
			<div>
				<input id="clip_set_start" type="button" value="Set Start" />
				<input id="clip_set_stop" type="button" value="Set Stop" />
				
			</div>
            </div>
			
		</div>

		<div style="float:left;padding-left:10px;">
			<div>
			    <h2>Title</h2>
				<input style="width:100%;" type="text" name="jform[title]" id="jform_title" value="<?php echo $this->entry_data[0]->title;?>" /> 
				<hr />
				<?php
				foreach ($info as $key => $val ) {
					echo "<strong>".$key."</strong> : ". $val." <br />";
					
				}
				?>
				
				<br /><h2>Control</h2>
				<div id="take_control_buttons" style="text-align:left;">
					<?php if ($this->entry_data[0]->state <= 2 ) { ?>
					
						<?php if ( strpos($info["Recording Format"], "HLS") !== false ) { ?>
							<?php if ( $this->settings["remote_url"] != "" ) { ?>
								<hr />
								<strong>Publish to remote server</strong>
								<br />
								<strong>Publishing to: </strong><?php echo $this->settings["remote_url"]; ?>
								<div id="publishlive_info"></div>
								<br />
								<br />
								
								<div style="text-align:right;">
									<input id="button_togglelive" type="button" value="Go Live!" />
								</div>
							<?php } ?>
						<?php } ?>
						<br /><hr />
						<strong>Stop recording: </strong>
						<div style="text-align:right;">
							<input id="button_stoptake" type="button" value="Stop Take" />
						</div>
						
					<?php } ?>
					<hr />
					<strong>Create a thumb: </strong>
					<div style="text-align:right;">
						<input id="create_thumbs" type="button" value="Create Thumb" />
					</div>
				</div>
			</div>
			<br /><br />
			<button style="width:100%;" type="submit" class="button"><?php echo JText::_('Save Title, Notes & Close'); ?></button>
		</div>	
	</div>
	<div style="clear:both;"></div>

	<?php echo $this->form->renderFieldSet("hidden"); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="jform[pid]" value="<?php echo $this->entry_data[0]->pid; ?>" />
    <input type="hidden" name="view" value="takes" />
    <input type="hidden" name="task" value="modify" />
    <input type="hidden" name="takes_id" value="<?php echo $this->takes_id; ?>" />

</form>



