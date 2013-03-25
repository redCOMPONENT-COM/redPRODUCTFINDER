<?php
/** 
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );?>
<div><?php echo JHTML::_('image', JURI::root().'media/com_redproductfinder/redproductfinder_logo_400width.png', JText::_('redPRODUCTFINDER')); ?></div>
<table class="adminlist">
<thead>
	<tr>
		<th><?php echo JText::_('NAME'); ?></th>
		<th><?php echo JText::_('TOTAL'); ?></th>
	</tr>
</thead>
<tbody>
	<?php foreach ($this->stats as $key => $stat) { ?>
		<tr>
			<td><?php echo JText::_($key); ?></td>
			<td><?php echo $stat['total']; ?></td>
		</tr>
	<?php } ?>
</table>
