<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_radenium
 *
 * @copyright   Copyright (C) 2017 Andries Bron, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 		
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 		
?>

        <?php 
/*
echo "<pre>";
print_r( $this->live_now ); 
echo "<h2>Media</h2>";
print_r($this->media);
echo "</pre>";

foreach($this->media["media"] as $m ) {
echo "<div>";
//echo "<video controls width=\"320\" height=\"240\">";
echo "<video controls>";
echo "  <source src=\"".$m."\" type=\"video/mp4\">";
echo "</video>";
echo "</div>";
}
*/
//print_r($this->entry_data[0]);
$vidurl = "media/com_radenium/media/takes/id_".$this->entry_data[0]->id."/playlist.m3u8";
//echo $vidurl;
echo "<a href=\"".$vidurl."\">".$vidurl."</a>";
echo "<div>";
//echo "<video controls width=\"320\" height=\"240\">";
echo "<video controls>";
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
	}
}
foreach( $this->entry_data[0] as $key => $val )
        {
            $this->form->setValue( $key, null, $val);
        }
        ?><div id="takes_new">
<h1><?php echo JText::_('COM_radenium_VIEW_EDIT_ENTRY'); ?></h1>
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_takes" name="radenium_takes">
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_FIELDSET_TAKE'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("take"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_FIELDSET_LIVEPUBLISH'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("livepublish"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_FIELDSET_RUNNINGTAKE'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("runningtake"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_FIELDSET_HIDDEN'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("hidden"); ?>
        </div>
    </div>
    
    <br />

    <?php echo JHtml::_('form.token'); ?>
    
    <input type="hidden" name="view" value="takes" />
    <input type="hidden" name="task" value="modify" />
    <input type="hidden" name="takes_id" value="<?php echo $this->takes_id; ?>" />
    <br />
    <button type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
    </form>
</div>
