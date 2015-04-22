<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

?>
<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped" id="typeslist" class="adminlist">
		<thead>
			<tr>
				<th width="20">
				<?php echo JText::_('COM_REDPRODUCTFINDER_ID'); ?>
				</th>
				<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->types ); ?>);" />
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_NAME'); ?>
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_FORM_NAME'); ?>
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_SELECT'); ?>
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_ORDERING'); ?>
				<?php echo JHTML::_('grid.order',  $this->types ); ?>
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_PUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = $this->items[$i];

			JFilterOutput::objectHTMLSafe($row);
			$link 	= 'index.php?option=com_redproductfinder&task=type.edit&id='. $row->id;

			$checked = JHTML::_('grid.checkedout',  $row, $i);
			$my  = JFactory::getUser();
			?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td align="center">
				<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td>
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					?>
					<?php echo $row->type_name; ?>
					&nbsp;[ <i><?php echo JText::_('CHECKED_OUT'); ?></i> ]
					<?php
				} else {
					?>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_('EDIT_FIELD'); ?>">
					<?php echo $row->type_name; ?>
					</a>
					<?php
				}
				?>
				</td>
				<td>
					<?php echo $row->form_name; ?>
				</td>
				<td>
					<?php echo JText::_($row->type_select); ?>
				</td>
				<td>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
				</td>
				<td width="10%" align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, 'types.', 1, 'cb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
	            <td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
	        </tr>
	    </tfoot>
		</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHtml::_('form.token'); ?>
</form>
