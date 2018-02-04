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

<div id="view_ffmpeg_layout_default">
    <h1><?php echo JText::_('COM_RADENIUM_VIEW_FFMPEG_LAYOUT_NEW_TITLE'); ?></h1>
    <hr />
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="ffmpeg" name="ffmpeg">

    <input type="hidden" name="view" value="ffmpeg" />
<div>
    <button type="submit" name="layout" value="new" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_NEW_ENTRY'); ?></button>
    &nbsp;<button type="submit" name="task" value="edit" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_EDIT_ENTRY'); ?></button>
    &nbsp;<button type="submit" name="task" value="delete" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_DELETE_ENTRY'); ?></button>

</div>

<div>

    <?php

    foreach ( $this->ffmpeg_entries as $entry ) {
            $ffmpeg_id = $entry->id;        ?>
        <input type="checkbox" name="ffmpeg_id" value="<?php echo $ffmpeg_id; ?>" />

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
