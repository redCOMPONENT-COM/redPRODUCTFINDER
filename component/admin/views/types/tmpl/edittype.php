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
function datepickerValidation(amt)
{
	if((amt=="Productfinder datepicker")){
		document.getElementById('showdiv').style.display = 'block';
	}else{
		document.getElementById('showdiv').style.display = 'none';
	}
}
</script>
<form action="index.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TYPE_NAME_TIP'), JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'); ?>
			</td>
			<td>
				<input class="inputbox" type="text" size="80" name="type_name" value="<?php echo $this->row->type_name; ?>">
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_FORM_NAME'), JText::_('COM_REDPRODUCTFINDER_FORM_NAME'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_FORM_NAME'); ?>
			</td>
			<td>
				<?php echo $this->lists['form_id']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TYPE_SELECT_TIP'), JText::_('COM_REDPRODUCTFINDER_TYPE_SELECT'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_SELECT'); ?>
			</td>
			<td>
				<?php echo $this->lists['type_select']; ?>
			</td>
		</tr>
		<?php
		
			if($this->lists['type_select']=="Productfinder datepicker" || $this->row->type_select=="Productfinder datepicker"){
				$display = 'style="display:block;"';
			}else{
				$display = 'style="display:none;"';
			}
		?>
<tr>
	<td colspan="2">
		<div id="showdiv"  <?php echo $display;?>>
		<table cellspacing="0" cellpadding="0" border="0" width="38%">
			<tr>
				<td>
					<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TYPE_SELECTFIELD_TIP'), JText::_('COM_REDPRODUCTFINDER_TYPE_SELECT_FIELD'), 'tooltip.png', '', '', false); ?>
					<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_SELECT_FIELD'); ?>
				</td>
				<td>
					<?php echo $this->lists['extrafield']; ?>
				</td>
			</tr>
			
			<tr>
				<td>
					<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TYPE_STYLEOFPICKER_TIP'), JText::_('COM_REDPRODUCTFINDER_TYPE_STYLE_PICKER'), 'tooltip.png', '', '', false); ?>
					<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_STYLE_PICKER'); ?>
				</td>
				<td>
					<?php echo $this->lists['picker']; ?>
				</td>
			</tr>
	</table>
			</div>
	</td>
</tr>	
			
		<tr>
			<td>
				<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TOOLTIP_TIP'), JText::_('TOOLTIP'), 'tooltip.png', '', '', false); ?>
				<?php echo JText::_('COM_REDPRODUCTFINDER_TOOLTIP'); ?>
			</td>
			<td>
				<textarea name="tooltip" cols="80" rows="10"><?php echo $this->row->tooltip; ?></textarea>
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
	<?php if (isset($this->row->id)) { ?><input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /><?php } ?>
	<?php if (isset($this->row->ordering)) { ?><input type="hidden" name="ordering" value="<?php echo $this->row->ordering; ?>" /><?php } ?>
	<input type="hidden" name="option" value="com_redproductfinder" />
	<input type="hidden" name="task" value="types" />
	<input type="hidden" name="controller" value="types" />
</form>
