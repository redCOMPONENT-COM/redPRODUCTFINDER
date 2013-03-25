<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');
?>
<form action="index.php" method="post" name="adminForm">
	<?php $pane = JPane::getInstance('tabs');
	echo $pane->startPane("settings");
	echo $pane->startPanel( JText::_('Form'), 'form_tab' );
	$row = 0;
	?>
	<div class="col50"><fieldset class="adminform">
		<table class="adminform">
		<tr class="row<?php echo $row; ?>">
			<td>
				<span class="hasTip" title="<?php echo JText::_('Give the form a name');?>"><?php echo JText::_('Form name'); ?></span>
			</td>
			<td>
			<input class="inputbox" type="text" size="40" name="formname" value="<?php echo $this->row->formname; ?>">
			</td>
		</tr>
		<tr class="row<?php echo $row = 1 - $row; ?>">
			<td>
				<span class="hasTip" title="<?php echo JText::_('Set to show the form name on the form');?>"><?php echo JText::_('Show form name'); ?></span>
			</td>
			<td>
			 <?php echo $this->lists['showname']; ?>
			</td>
		</tr>
		<tr class="row<?php echo $row = 1 - $row; ?>">
			<td>
				<span class="hasTip" title="<?php echo JText::_('Set class name to allow individual styling');?>"><?php echo JText::_('CSS class name'); ?></span>
			</td>
			<td>
			 <input class="inputbox" type="text" size="40" name="classname" value="<?php echo $this->row->classname; ?>">
			</td>
		</tr>
		<tr class="row<?php echo $row = 1 - $row; ?>">
			<td valign="top" align="right">
			<?php echo JText::_('Published'); ?>
			</td>
			<td>
			<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		<tr class="row<?php echo $row = 1 - $row; ?>">
			<td valign="top" align="right">
			<?php echo JText::_('Dependancies'); ?>
			</td>
			<td>
			<?php echo $this->lists['dependency']; ?>
			</td>
		</tr>
		</table>
	</fieldset></div>	
	<?php
	echo $pane->endPanel();
	echo $pane->endPane();
	?>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="com_redproductfinder" />
	<input type="hidden" name="task" value="forms" />
	<input type="hidden" name="controller" value="forms" />
</form>
