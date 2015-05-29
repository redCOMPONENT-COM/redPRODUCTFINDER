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
		$items = $this->get('Items');
		$state = $this->get('state');

		/* Get the used types */
		$types = $this->get('TagTypeNames');

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('tags', $tags);
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
		JToolBarHelper::title(JText::_( 'Tag' ), 'address contact');
		JToolbarHelper::addNew('tag.add');
		JToolbarHelper::editList('tag.edit');
		JToolbarHelper::publish('tags.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('tags.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('Are you sure you want to delete items', 'tags.delete');
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
			't.tag_name' => JText::_('JGLOBAL_TITLE'),
			't.published' => JText::_('JSTATUS'),
			't.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
?>