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

<div id="takes_new">
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="radenium_takes" name="radenium_takes">
    <div class="form_rendered_container">
        <h3><?php echo JText::_('LIVEPUBLISH'); ?></h3>
        <div class="form_rendered_container_form">
            <?php $this->form->renderFieldSet("livepublish"); ?>
        </div>
    </div>
    
    <br />

    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="new" />
    <br />
    <button type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
    </form>
</div>
