<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Types view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * Tags View
 */
class RedproductfinderViewTypes extends JViewLegacy
{
	function display($tpl = null)
	{
		/* add submenu here */
		RedproductfinderHelper::addSubmenu("types");

		/* Get the pagination */
		$pagination = $this->get('Pagination');

		/* Get the fields list */
		$types = $this->get('Types');
		$items = $this->get('Items');
		$state = $this->get("State");

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('types', $types);
		$this->assignRef('items', $items);
		$this->assignRef('state', $state);

		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JToolBarHelper::title(JText::_( 'Type' ), 'address contact');
		JToolbarHelper::addNew('type.add');
		JToolbarHelper::editList('type.edit');
		JToolbarHelper::publish('types.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('types.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('Are you sure you want to delete items', 'types.delete');
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			't.type_name' => JText::_('JGLOBAL_TITLE'),
			't.published' => JText::_('JSTATUS'),
			't.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
?>
