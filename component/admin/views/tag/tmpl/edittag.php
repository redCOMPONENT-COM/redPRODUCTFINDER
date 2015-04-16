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
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (form.type_id.value == "") 
	{
		alert( "<?php echo "Select ".JText::_ ( 'TYPE_NAME', true );?>" );
		return false;
	}
	submitform( pressbutton );
	
}
</script>
<form action="index.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TAG_NAME_TIP'), JText::_('COM_REDPRODUCTFINDER_TAG_NAME'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_TAG_NAME'); ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="tag_name" value="<?php echo $this->row->tag_name; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TYPE_NAME_TIP'), JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'); ?>
			</td>
			<td>
			<?php echo $this->lists['types']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TAG_ALIASES'), JText::_('COM_REDPRODUCTFINDER_TAG_ALIASES'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_TAG_ALIASES'); ?>
			</td>
			<td>
				<input type="text" name="aliases" id="aliases" value="<?php echo $this->row->aliases;?>" > 
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_PUBLISHED_TIP'), JText::_('COM_REDPRODUCTFINDER_PUBLISHED'), 'tooltip.png', '', '', false); ?>
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
	<input type="hidden" name="task" value="tags" />
	<input type="hidden" name="controller" value="tags" />
	<input type="hidden" name="filtertype" value="<?php echo JRequest::getInt('filtertype', 0); ?>" />
</form>
