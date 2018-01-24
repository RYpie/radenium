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


?><h1><?php echo JText::_('COM_RADENIUM_VIEW_LIVE_LAYOUT_LIVE_NOW'); ?></h1><?php

?>
encoder task toevoegen onder programma naam en episode naam. 
Fields maken voor devices en voor de video types.
Description ook toevoegen.
kweetniet, displayen zonder database? ik denk het  toch niet...
Lijst ophalen, alleen wat live gepublised wordt hoeft niet de database in, want immers dat wordt al ge-encode.


<?php
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

?>

<div id="view_live_layout_default">
    <h1><?php echo JText::_('COM_RADENIUM_VIEW_LIVE_LAYOUT_NEW_TITLE'); ?></h1>
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="live" name="live">

    <input type="hidden" name="view" value="live" />
<div>
    <button type="submit" name="layout" value="new" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_NEW_ENTRY'); ?></button>
    &nbsp;<button type="submit" name="task" value="edit" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_EDIT_ENTRY'); ?></button>
    &nbsp;<button type="submit" name="task" value="delete" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_DELETE_ENTRY'); ?></button>

</div>

<div>

    <?php

    foreach ( $this->live_entries as $entry ) {
            $live_id = $entry->id;        ?>
        <input type="checkbox" name="live_id" value="<?php echo $live_id; ?>" />

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
