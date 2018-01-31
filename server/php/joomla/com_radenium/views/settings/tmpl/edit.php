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
 		
?>

        <?php 

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
        ?><div id="settings_new">
<h1><?php echo JText::_('COM_radenium_VIEW_EDIT_ENTRY'); ?></h1>
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_settings" name="radenium_settings">
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_SETTINGS_FIELDSET_CHANNEL'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("channel"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_SETTINGS_FIELDSET_FFMPEG'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("ffmpeg"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_SETTINGS_FIELDSET_REMOTE'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("remote"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_SETTINGS_FIELDSET_RTSP_SERVER'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("rtsp_server"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_SETTINGS_FIELDSET_HIDDEN'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("hidden"); ?>
        </div>
    </div>
    
    <br />

    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="modify" />
    <input type="hidden" name="settings_id" value="<?php echo $this->settings_id; ?>" />
    <br />
    <button type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
    </form>
</div>
