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
 		
$vidurl = "media/com_radenium/media/takes/id_".$this->entry_data[0]->id."/playlist.m3u8";
//$vidurl = "index.php?option=com_radenium&view=m3u8&format=raw&take_id=".$this->entry_data[0]->id;
//$m3u8_status = "index.php?option=com_radenium&view=m3u8&task=getstatus&format=raw&take_id=".$this->entry_data[0]->id;
//$m3u8_status = "index.php?option=com_radenium&view=takes&task=m3u8status&format=raw&take_id=".$this->entry_data[0]->id;

$m3u8_file = "index.php?option=com_radenium&view=m3u8&format=raw&take_id=".$this->entry_data[0]->id;
$m3u8_file = "media/com_radenium/playlist.php?take_id=".$this->entry_data[0]->id;

//echo "<a href=\"".$vidurl."\">".$vidurl."</a>";
echo "<div>";
//echo "<video controls width=\"320\" height=\"240\">";
//echo "<video controls autoplay=\"1\">";
echo "<video controls width=\"100%\" autoplay=\"1\">";
echo "  <source src=\"".$vidurl."\" type=\"video/mp4\">";
echo "</video>";
echo "</div>"; 

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

//Get custom field
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


foreach( $this->entry_data[0] as $key => $val )
{
	$this->form->setValue( $key, null, $val);
}
//print_R($this->entry_data[0]->id);
/*
echo "<pre>";
print_r($this->entry_data[0]);
echo "</pre>";
*/
$jformpublish=array(
	0=>""
	,1=>""
);



        ?><div id="takes_new">

<div id="take_control_buttons">
<?php if ($this->entry_data[0]->state < 2 ) { ?>
<?php if (strpos($info["Recording Format"], "HLS")) { ?>
<?php 
$pub = array( 
	0 => "Start publishing"
	, 1 => "Stop publishing"
	);
?>


<button id="button_togglelive"><?php echo $pub[intval($this->entry_data[0]->publish)]; ?> Live!</button> 
<?php } ?>

<button id="button_stoptake">Stop Take</button>
<br /><br />
<?php } ?>
</div>

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
	
});

jQueryRepresentatives(document.body).on('click','#button_togglelive', function(){
	jQueryRepresentatives.ajax({
		type: 'GET',
		url: "http://localhost:8888/radenium/index.php?option=com_radenium&amp;view=takes&amp;format=raw&amp;task=togglepublishlive&amp;takes_id=<?php echo $this->entry_data[0]->id; ?>",
		success:function(data){
			//jQueryRepresentatives('#results').html(data);
		},
		error:function(){
			//jQueryRepresentatives('#results').html('<p class="error">An error was encountered while retrieving the representatives from the database.</p>');
		}
	});
	
});
</script>
<div "take_details" style="background-color:#f3f3ee;padding:5px;">
<table><tr><td valign="top">
<?php
$row_counter = 0;

foreach ($info as $key => $val ) {
	if ( fmod( $row_counter, 3 ) == 0) {

	}
	echo "<strong>".$key."</strong> : ". $val." <br />";
	$row_counter += 1;
	
	if ( fmod( $row_counter,3 ) == 0) {
		echo "</td>";
		echo "<td>&nbsp;</td>";
		echo "<td valign=\"top\">";
	}
	
	
}
?>
</td></tr></table>
</div> 

<form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_takes" name="radenium_takes">


    <div class="form_rendered_container">
    	<h3>Take Information &amp; Notes:</h3>
    	
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("taketitle"); ?>
        </div> 
        

   
       
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("information"); ?>
        </div>
    </div>
    
    
	<?php echo $this->form->renderFieldSet("hidden"); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="jform[pid]" value="<?php echo $this->entry_data[0]->pid; ?>" />
    <input type="hidden" name="view" value="takes" />
    <input type="hidden" name="task" value="modify" />
    <input type="hidden" name="takes_id" value="<?php echo $this->takes_id; ?>" />
    <br />
    <button type="submit" class="button"><?php echo JText::_('Save & Close'); ?></button>
    </form>
</div>
