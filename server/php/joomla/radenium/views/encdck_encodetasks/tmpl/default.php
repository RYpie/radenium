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

<div id="view_encdck_encodetasks_layout_default">
    <h1><?php echo JText::_('COM_RADENIUM_VIEW_ENCDCK_ENCODETASKS_LAYOUT_NEW_TITLE'); ?></h1>
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="encdck_encodetasks" name="encdck_encodetasks">

    <input type="hidden" name="view" value="encdck_encodetasks" />
<div>
    <button type="submit" name="layout" value="new" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_NEW_ENTRY'); ?></button>
    &nbsp;<button type="submit" name="task" value="edit" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_EDIT_ENTRY'); ?></button>
    &nbsp;<button type="submit" name="task" value="delete" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_DELETE_ENTRY'); ?></button>

</div>

<div>

    <?php

    foreach ( $this->encdck_encodetasks_entries as $entry ) {
            $encdck_encodetasks_id = $entry->id;        ?>
        <input type="checkbox" name="encdck_encodetasks_id" value="<?php echo $encdck_encodetasks_id; ?>" />

        <?php
        print_r($entry);
        echo "<br />";
        ?>
        <div class="clr">&nbsp;</div><?php
    }

    ?>
</div>    <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
