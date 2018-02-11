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

JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

$res = JFormHelper::loadFieldType('ScreenResolution', false);
$res = $res->getOptions(); // works only if you set your field getOptions on public!!

$stateinfo=array(
		0 => array("caption"=>"Running", "bg_color"=>"#F00")
		, 1 => array("caption"=>"Stopped", "bg_color"=>"#F00")
		, 2 => array("caption"=>"Stopped", "bg_color"=>"#0C0")	
);

$liveinfo=array(
		0 =>array("caption"=>"Not Live", "bg_color"=>"#F00")
		, 1 =>array("caption"=>"LIVE!", "bg_color"=>"#0C0")
);


?>

<div id="view_takes_layout_default">
    <h1><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_LAYOUT_NEW_TITLE'); ?></h1>
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="takes" name="takes">
	    <input type="hidden" name="view" value="takes" />
		<button type="submit" name="layout" value="new" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_NEW_ENTRY'); ?></button>
	    &nbsp;<button type="submit" name="task" value="edit" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_EDIT_ENTRY'); ?></button>
	    &nbsp;<button type="submit" name="task" value="delete" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_DELETE_ENTRY'); ?></button>
		<br />
		<br />
		<table class="radenium_table" style="width:100%;">
			<tr style="text-align:left;"><th></th><th>Id</th><th>Title</th><th>Date</th><th>Resolution</th><th>State</th><th>Live</th></tr>	
		    <?php
		    $rowcount = 0;
		    foreach ( $this->takes_entries as $entry ) {
	            $takes_id = $entry->id;
	            if (fmod($rowcount,2) == 0 ) {
	            	$row = 0;
	            } else {
	            	$row = 1;
	            }
			?>
	   		<tr class="radenium_table_row<?php echo $row; ?>">
	        <td valign="top"> <input type="checkbox" name="takes_id[]" value="<?php echo $takes_id; ?>" /> </td>
	        <?php
		        echo "<td>".$entry->id." </td>";
		        echo "<td><a href=\"index.php?option=com_radenium&view=takes&layout=edit&takes_id=".$takes_id."&Itemid=105\">".$entry->title."</a> </td>";
		        echo "<td>".$entry->takedate." </td>";
		        echo "<td>".$res[$entry->resolution]." </td>";
		        echo "<td>".$stateinfo[intval($entry->state)]["caption"]." </td>";
		        echo "<td>".$liveinfo[intval($entry->publish)]["caption"]." </td>";
	        ?>
			</tr>
		    <?php
		    	$rowcount += 1;
		    }
		    ?>
	    </table>   
		<?php echo JHtml::_('form.token'); ?>
    </form>
</div>
