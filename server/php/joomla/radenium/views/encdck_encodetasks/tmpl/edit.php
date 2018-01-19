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
        ?><div id="encdck_encodetasks_new">
<h1><?php echo JText::_('COM_radenium_VIEW_EDIT_ENTRY'); ?></h1>
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_encdck_encodetasks" name="radenium_encdck_encodetasks">
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_ENCDCK_ENCODETASKS_FIELDSET_TASK'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("task"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_ENCDCK_ENCODETASKS_FIELDSET_LIVESTREAMING'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("livestreaming"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_ENCDCK_ENCODETASKS_FIELDSET_RUNNINGTASK'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("runningtask"); ?>
        </div>
    </div>
    
    <br />
    <div class="form_rendered_container">
        <h3><?php echo JText::_('COM_RADENIUM_VIEW_ENCDCK_ENCODETASKS_FIELDSET_HIDDEN'); ?></h3>
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("hidden"); ?>
        </div>
    </div>
    
    <br />

    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="modify" />
    <input type="hidden" name="encdck_encodetasks_id" value="<?php echo $this->encdck_encodetasks_id; ?>" />
    <br />
    <button type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
    </form>
</div>
