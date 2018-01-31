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
		, 1 => array("caption"=>"Running", "bg_color"=>"#F00")
		, 2 => array("caption"=>"Stopped", "bg_color"=>"#0C0")	
);

$liveinfo=array(
		0 =>array("caption"=>"Not Live", "bg_color"=>"#F00")
		, 1 =>array("caption"=>"LIVE!", "bg_color"=>"#0C0")
);
?>

<div id="view_takes_layout_default">
    <h1><?php echo JText::_('COM_RADENIUM_VIEW_TAKES_LAYOUT_NEW_TITLE'); ?></h1>   
    <hr />
    <form class="form-validate" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="takes" name="takes">

    <input type="hidden" name="view" value="takes" />
	<div>
	    <button type="submit" name="layout" value="new" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_NEW_ENTRY'); ?></button>
	    &nbsp;<button type="submit" name="task" value="edit" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_EDIT_ENTRY'); ?></button>
	    &nbsp;<button type="submit" name="task" value="delete" class="button"><?php echo JText::_('COM_RADENIUM_VIEW_BUTTON_DELETE_ENTRY'); ?></button>
	</div>
<br />
	<div style="width:400px;">
	    <div style="display:flex;align-items: stretch;justify-content: space-around;flex-grow: 1;">
			<div>Select</div><div>Take Date</div><div>Resolution</div><div>State</div><div>Live</div>
		</div>
	    <?php
	
	    foreach ( $this->takes_entries as $entry ) {
	            $takes_id = $entry->id;        ?>
	    <div style="display:flex;flex-flow: row wrap;align-items: stretch;justify-content: space-around;flex-grow: 1;">
	        <div><input type="checkbox" name="takes_id[]" value="<?php echo $takes_id; ?>" /> </div>
	        <?php
	        //print_r($entry);
	
	        echo "<div>".$entry->takedate." </div>";
	        echo "<div>".$res[$entry->resolution]." </div>";
	        echo "<div>".$stateinfo[intval($entry->state)]["caption"]." </div>";
	        echo "<div>".$liveinfo[intval($entry->publish)]["caption"]." </div>";
	        ?>
		</div>
		<div class="clr"></div>
	    <?php
	    }
	
	    ?>
    
	</div>    
	<?php echo JHtml::_('form.token'); ?>
    </form>
</div>
