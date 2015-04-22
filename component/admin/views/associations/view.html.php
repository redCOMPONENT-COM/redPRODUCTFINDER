<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Products view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');
/**
 * Associations View
 */
class RedproductfinderViewAssociations extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		/* add submenu here */
		RedproductfinderHelper::addSubmenu("associations");

		/* Get the pagination */
		$pagination = $this->get('Pagination');

		/* Get the fields list */
		$associations = $this->get('Associations');

		/* Get the fields list */
		$tags = $this->get('AssociationTagNames');

		/* Check if there are any forms */
		$countassociations = $this->get('Total');

		$items	= $this->get("Items");

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('associations', $associations);
		$this->assignRef('tags', $tags);
		$this->assignRef('lists', $lists);
		$this->assignRef('countassociations', $countassociations);
		$this->assignRef('items', $items);

		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JToolBarHelper::title(JText::_( 'Association' ), 'address contact');
		JToolbarHelper::addNew('association.add');
		JToolbarHelper::editList('association.edit');
		JToolbarHelper::publish('associations.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('associations.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::trash('associations.trash');
	}
}
?>
