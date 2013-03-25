<?php

/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

?>
<form action="index.php" method="post" name="adminForm">
	<table class="adminlist">
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
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->forms ); $i < $n; $i++) {
			$row = $this->forms[$i];
			
			JFilterOutput::objectHTMLSafe($row);
			$link 	= 'index.php?option=com_redproductfinder&task=edit&controller=forms&hidemainmenu=1&cid[]='. $row->id;

			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? JText::_('Published') : JText::_('Unpublished');
			
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
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="../images/<?php echo $img;?>" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
				<td>
				<?php 
					echo "{redproductfinder}".$row->id."{/redproductfinder}";
				?>
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
	<input type="hidden" name="option" value="com_redproductfinder" />
	<input type="hidden" name="task" value="forms" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="forms" />
</form>
