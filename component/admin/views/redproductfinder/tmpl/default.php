<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die;
?>

<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="span12">
		<div><?php echo JHTML::_('image', JURI::root().'media/com_redproductfinder/redproductfinder_logo_400width.png', JText::_('redPRODUCTFINDER')); ?></div>

		<table class="table table-striped" id="typeslist" class="adminlist">
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
	</div>

</form>