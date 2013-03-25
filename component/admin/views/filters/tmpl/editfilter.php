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
	if(pressbutton=='cancel')
	{
		submitform( pressbutton );
	}
	var form = document.adminForm;
	var total=0;
	var a=form['tag_id[]'];
	for(i=0;i<a.length;i++)
	{
		if(a[i].checked)
		{
			total++;
		}	
	}	
	if(pressbutton=='save' || pressbutton=='apply')
	{
		if(total==0)
		{
			alert("<?php echo "Please Check atleast one ".JText::_ ( 'TYPE_NAME', true );?>");
			return false;
			
		}
	
		submitform( pressbutton );	
	}
}
</script>
<form action="index.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_FILTER_NAME'), JText::_('COM_REDPRODUCTFINDER_FILTER_NAME'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_FILTER_NAME'); ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="filter_name" value="<?php echo $this->row->filter_name; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_SELECTOR_TYPE'), JText::_('COM_REDPRODUCTFINDER_SELECTOR_TYPE'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_SELECTOR_TYPE'); ?>
			</td>
			<td>
				<?php echo $this->lists['type_select']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_NEURATRAL_SELECTION_TEXT'), JText::_('COM_REDPRODUCTFINDER_NEURATRAL_SELECTION_TEXT'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_NEURATRAL_SELECTION_TEXT'); ?>
			</td>
			<td>
				<input class="inputbox" type="text" size="80" name="select_name" value="<?php echo $this->row->select_name; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JHTML::tooltip(JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'), JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'), 'tooltip.png', '', '', false); ?>
			<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'); ?>
			</td>
			<td>
			<?php
				$modal = $this->getModel();
				$tagids =array();
				 if($this->row->tag_id)
					$tagids = explode(",",$this->row->tag_id);
				for($i=0;$i<count($this->lists['type']);$i++)
				{
					$types = & $this->lists['type'][$i];
					echo "<b>".$types->type_name."</b><br />";
					$tags = $modal->getTags($types->id);

					for($j=0;$j<count($tags);$j++)
					{
						$checked = in_array($tags[$j]->tag_id,$tagids) ? 'checked' : "";
 						echo "&nbsp;<input type='checkbox' name='tag_id[]' value='".$tags[$j]->tag_id."' ".$checked." />".$tags[$j]->tag_name;
					}
 					echo "<br />";
				}
			?>
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
	<input type="hidden" name="task" value="filters" />
	<input type="hidden" name="controller" value="filters" />
	<input type="hidden" name="filtertype" value="<?php echo JRequest::getInt('filtertype', 0); ?>" />
</form>
