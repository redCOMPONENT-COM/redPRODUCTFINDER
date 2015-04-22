<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Tags view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * Tags View
 */
class RedproductfinderViewFilters extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		global $mainframe;
		/* Get the task */
		$task = JRequest::getCmd('task');

		/* add submenu here */
		RedproductfinderHelper::addSubmenu("filters");

		/* Get the pagination */
		$pagination = $this->get('Pagination');

		/* Get the tags */
		$filters = $this->get('Filters');

		/* Check if there are any forms */
		$countfilters = $this->get('Total');

		$items = $this->get("items");

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('filters', $filters);
		$this->assignRef('countfilters', $countfilters);
		$this->assignRef('items', $items);

		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JToolBarHelper::title(JText::_( 'Filters' ), 'address contact');
		JToolbarHelper::addNew('filter.add');
		JToolbarHelper::editList('filter.edit');
		JToolbarHelper::publish('filter.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('filters.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::trash('filters.trash');
	}
}
?>