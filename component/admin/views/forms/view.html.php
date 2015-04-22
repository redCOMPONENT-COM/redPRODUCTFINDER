<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redFORM view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * redFORM View
 */
class RedproductfinderViewForms extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		/* add submenu here */
		RedproductfinderHelper::addSubmenu("forms");

		$pagination = $this->get('Pagination');

		/* Get the competitions list */
		$forms = $this->get('Forms');
		$items = $this->get("Items");

		/* Set variabels */
		$this->assignRef('pagination',   $pagination);
		$this->assignRef('forms',   $forms);
		$this->assignRef('items',   $items);

		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JToolBarHelper::title(JText::_( 'Forms' ), 'address contact');
		JToolbarHelper::addNew('form.add');
		JToolbarHelper::editList('form.edit');
		JToolbarHelper::publish('forms.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('forms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		// JToolbarHelper::deleteList(JText::_('Are you sure you want to delete the form and all related fields and values?'), 'forms.delete', 'JTOOLBAR_EMPTY_TRASH');
		JToolbarHelper::trash('forms.trash');
	}
}
?>
