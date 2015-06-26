<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$model = JModelLegacy::getInstance("Filters", "RedproductfinderModel");

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$sortFields = $this->getSortFields();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");

		order = table.options[table.selectedIndex].value;

		if (order != "' . $listOrder . '")
		{
			dirn = "asc";
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn, "");
	};
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="span12 j-toggle-main">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_REDPRODUCTFINDER_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>

		<div class="clearfix"></div>

		<table class="table table-striped" id="filterslist" class="adminlist">
			<thead>
			<tr>
				<th width="20">
				<?php echo JText::_('COM_REDPRODUCTFINDER_ID'); ?>
				</th>
				<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->filters ); ?>);" />
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_FILTER_NAME'); ?>
				</th>
				<th class="title">
				<?php echo JText::_('COM_REDPRODUCTFINDER_TAGS'); ?>
				</th>
				<th class="title">
				<?php echo JText::_('Ordering'); ?>
				<?php echo JHTML::_('grid.order',  $this->filters ); ?>
				</th>
				<th class="title">
				<?php echo JText::_('Published'); ?>
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
				$link 	= 'index.php?option=com_redproductfinder&task=filter.edit&id='. $row->id;

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
						<?php echo $row->tag_name; ?>
						&nbsp;[ <i><?php echo JText::_('Checked Out'); ?></i> ]
						<?php
					} else {
						?>
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('Edit field'); ?>">
						<?php echo $row->filter_name; ?>
						</a>
						<?php
					}
					?>
					</td>
					<td>
						<?php
							echo $model->getTagname($row->tag_id);
						 ?>
					</td>
					<td>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
					</td>
					<td width="10%" align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, 'filters.', 1, 'cb', $row->publish_up, $row->publish_down); ?>
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
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="filters" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo JHtml::_('form.token'); ?>

</form>