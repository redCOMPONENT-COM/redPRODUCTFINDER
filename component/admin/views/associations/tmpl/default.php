<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if ($this->countassociations == 0)
{
	echo JText::_('No associations found');
}
else
{
?>
<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped" id="associationslist" class="adminlist">
		<thead>
		<tr>
			<th width="20">
			<?php echo JText::_('COM_REDPRODUCTFINDER_ID'); ?>
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->associations ); ?>);" />
			</th>
			<th class="title">
			<?php echo JText::_('COM_REDPRODUCTFINDER_ASSOCIATION'); ?>
			</th>
			<th class="title">
			<?php echo JText::_('COM_REDPRODUCTFINDER_TAG_NAME'); ?>
			</th>
			<th class="title">
			<?php echo JText::_('Ordering'); ?>
			<?php echo JHTML::_('grid.order',  $this->associations ); ?>
			</th>
			<th class="title">
			<?php echo JText::_('Published'); ?>
			</th>
		</tr>
		</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = $this->items[$i];

			JFilterOutput::objectHTMLSafe($row);
			$link 	= 'index.php?option=com_redproductfinder&task=association.edit&id='. $row->id;

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
					if ( $row->checked_out && ( $row->checked_out != $my->id ) )
					{
						?>
						<?php echo $row->product_name; ?>
						&nbsp;[ <i><?php echo JText::_('Checked Out'); ?></i> ]
						<?php
					}
					else
					{
						?>
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('Edit association'); ?>">
						<?php echo $row->product_name; ?>
						</a>
						<?php
					}
				?>
				</td>
				<td>
					<?php
						if (isset($this->tags[$row->id]))
						{
							foreach ($this->tags[$row->id] as $productid => $tag)
							{
								echo $tag.'<br />';
							}
						};
					?>
				</td>
				<td>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
				</td>
				<td width="10%" align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, 'associations.', 1, 'cb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		<tr>
            <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
         </tr>
		</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHtml::_('form.token'); ?>
</form>
<?php } ?>