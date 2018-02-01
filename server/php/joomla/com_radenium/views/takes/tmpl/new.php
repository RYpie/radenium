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
?>
<div id="takes_new">
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo "index.php?option=com_radenium&view=takes&layout=default"; /*JRoute::_('index.php'); */?>" method="post" id="radenium_takes" name="radenium_takes">
    <h3><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_FIELDSET_TAKE'); ?></h3>
    
    <div class="form_rendered_container">
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("taketitle"); ?>
        </div>
    </div>
    
    <div class="form_rendered_container">
        
        <div class="form_rendered_container_form">
            <?php echo $this->form->renderFieldSet("take"); ?>
        </div>
    </div>

    <?php 
    //echo $this->form->renderFieldSet("runningtake");
	//echo $this->form->renderFieldSet("hidden"); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="save" />
    <br />
    <button type="submit" class="button"><?php echo JText::_('Take!'); ?></button>
    <button type="submit" name="task" value="" class="button"><?php echo JText::_('Cancel'); ?></button>
    </form>
</div>
