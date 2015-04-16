<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHTML::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "contact.cancel" || document.formvalidator.isValid(document.getElementById("contact-form")))
		{
			Joomla.submitform(task, document.getElementById("redproductfinder-form"));
		}
	};
');
?>

<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">

	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div>
						<?php echo $this->form->renderField('formname'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('showname'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('classname'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('published'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('dependency'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('id'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="forms" />
	<?php echo JHtml::_('form.token'); ?>
</form>