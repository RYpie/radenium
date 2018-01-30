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

//echo "<a href=\"".$vidurl."\">".$vidurl."</a>";
echo "<div>";
//echo "<video controls width=\"320\" height=\"240\">";
echo "<video controls autoplay=\"1\">";
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
		, "Process ID"=> $this->entry_data[0]->pid
);


foreach( $this->entry_data[0] as $key => $val )
{
	$this->form->setValue( $key, null, $val);
}
print_R($this->entry_data[0]->id);
/*
echo "<pre>";
print_r($this->entry_data[0]);
echo "</pre>";
*/
$jformpublish=array(
	0=>""
	,1=>""
);
$jformpublish[intval($this->entry_data[0]->publish)] = "checked=\"checked\"";

        ?><div id="takes_new">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
function performCall(task) {
	//$.getJSON( "http://localhost:8888/radenium/test.json", function( data )
	$.getJSON( "http://localhost:8888/radenium/index.php?option=com_radenium&view=takes&format=raw&Itemid=105&task="+task+"&takes_id=<?php echo $this->entry_data[0]->id; ?>", function( data )
	{

		alert(data['msg']);
	});
	//alert("hi there");

}


</script>

<button onclick="performCall('publishlive')">Publish Live!</button> <button onclick="performCall('stoptake')">Stop Take</button>

<form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_takes" name="radenium_takes">

<br />
<div class="controls">
	<fieldset id="jform_publish" class="btn-group required radio" required aria-required="true"	>
		<input type="radio" id="jform_publish0" name="jform[publish]" value="0" <?php echo $jformpublish[0];?> required aria-required="true" />
			<label for="jform_publish0" >Stop Live</label>
		<input type="radio" id="jform_publish1" name="jform[publish]" value="1" <?php echo $jformpublish[1];?> required aria-required="true" />
			<label for="jform_publish1" >Go Live!</label>
	</fieldset>
</div>
    

<h2>Take information:</h2>
    <div class="form_rendered_container">
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("taketitle"); ?>
        </div>
    </div>
	<?php print_r($info); ?>
    <br />      
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_FIELDSET_NOTES'); ?></h3>
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
