<?php
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
JHTML::_('behavior.tooltip');
?>

<form action="index.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_PRODUCTNAME'), JText::_('COM_REDPRODUCTFINDER_PRODUCTNAME'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_PRODUCTNAME'); ?>
			</td>
			<td>
				<?php echo $this->lists['products']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_PRODUCT_ALIASES'), JText::_('COM_REDPRODUCTFINDER_PRODUCT_ALIASES'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_PRODUCT_ALIASES'); ?>
			</td>
			<td>
				<input type="text" name="aliases" id="aliases" value="<?php echo $this->row->aliases;?>" > 
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TAG_NAME'), JText::_('COM_REDPRODUCTFINDER_TAG_NAME'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_TAG_NAME'); ?>
			</td>
			<td>
				<?php echo $this->lists['tags']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_PUBLISHED'), JText::_('COM_REDPRODUCTFINDER_PUBLISHED'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_PUBLISHED'); ?>
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		</table>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="ordering" value="<?php echo $this->row->ordering; ?>" />
	<input type="hidden" name="option" value="com_redproductfinder" />
	<input type="hidden" name="task" value="associations" />
	<input type="hidden" name="controller" value="associations" />
</form>