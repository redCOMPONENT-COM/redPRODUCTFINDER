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
class RedproductfinderViewTags extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		/* add submenu here */
		RedproductfinderHelper::addSubmenu("tags");

		/* Get the pagination */
		$pagination = $this->get('Pagination');

		/* Get the fields list */
		$tags = $this->get('Tags');

		/* Get the used types */
		$types = $this->get('TagTypeNames');

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('tags', $tags);
		$this->assignRef('types', $types);

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
		JToolbarHelper::deleteList(JText::_('Are you sure you want to delete the form and all related fields and values?'), 'forms.delete', 'JTOOLBAR_EMPTY_TRASH');
		JToolbarHelper::trash('types.trash');

		JToolbarHelper::publish('types.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('types.unpublish', 'JTOOLBAR_UNPUBLISH', true);
	}
}
?>