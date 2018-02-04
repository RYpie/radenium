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

<div id="view_systemdevices_layout_default">
<h1><?php echo JText::_('COM_RADENIUM_VIEW_SYSTEMDEVICES_LAYOUT_NEW_TITLE'); ?></h1>
<br />
	<div style="width:100%;">
	<table style="width:100%;">
		<tr style="text-align:left;"><th>Id</th><th>Name</th><th>Type</th><th>System ID</th><th>ID String</th></tr>
		<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
	

<div>
    <?php

    foreach ( $this->systemdevices_entries as $entry ) {
        $systemdevices_id = $entry->id;  
        //print_r($entry);
        //echo "<br />";
        echo "<tr>";
        echo "<td>".$entry['id']."</td>";
        echo "<td>".$entry['name']."</td>";
        echo "<td>".$entry['type']."</td>";
        echo "<td>".$entry['sysid']."</td>";
        echo "<td>".$entry['idstr']."</td>";
        echo "</tr>";
    }

    ?>
    	</table>
	</div>

</div>
