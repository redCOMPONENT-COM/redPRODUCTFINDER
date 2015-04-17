<?php

/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

?>
<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm">
	<table  class="table table-striped" id="formslist" class="adminlist">
		<thead>
			<tr>
				<th width="20">
				<?php echo JText::_('COM_REDPRODUCTFINDER_ID'); ?>
				</th>
				<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->forms ); ?>);" />
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_FORM_NAME'); ?>
				</th>
				<th class="title">

				<?php echo JText::_('COM_REDPRODUCTFINDER_PUBLISHED'); ?>
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_TAG'); ?>
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
					$link 	= 'index.php?option=com_redproductfinder&task=form.edit&id='. $row->id;

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
								<?php echo $row->formname; ?>
								&nbsp;[ <i><?php echo JText::_('Checked Out'); ?></i> ]
								<?php
							} else {
								?>
								<a href="<?php echo $link; ?>" title="<?php echo JText::_('Edit form'); ?>">
								<?php echo $row->formname; ?>
								</a>
								<?php
							}
						?>
						</td>
						<td width="10%" align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'forms.', 1, 'cb', $row->publish_up, $row->publish_down); ?>
						</td>
						<td>
						<?php
							echo "{redproductfinder}".$row->id."{/redproductfinder}";
						?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				};

			?>
		</tbody>
        <tfoot>
        	<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="forms" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHtml::_('form.token'); ?>
</form>
