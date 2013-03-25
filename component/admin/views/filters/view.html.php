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

jimport( 'joomla.application.component.view' );

/**
 * Tags View
 */
class RedproductfinderViewFilters extends JView {
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null) {

		global $mainframe;
		/* Get the task */
		$task = JRequest::getCmd('task');
		switch ($task) {
			case 'apply':
			case 'edit':
			case 'add':
				if ($task == 'apply') $row = $this->get('SaveFilter');
				else $row = $this->get('Filter');

				if ($row) {
					/* Get the published field */
					$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published);
				}
				else $lists['published'] = '';

				/* Set the selector types */
				$generic = new StdClass();
				$generic->select_name = 'generic';
				$checkbox = new StdClass();
				$checkbox->select_name = 'checkbox';
				$radiobutton = new StdClass();
				$radiobutton->select_name = 'radiobutton';
				$type_select = array($generic, $checkbox,$radiobutton);
				$lists['type_select'] = JHTML::_('select.genericlist', $type_select, 'type_select', '', 'select_name', 'select_name', $row->type_select, false, true);

				$lists['type'] =$this->get('Types');

				/* Set variabels */
				$this->assignRef('row', $row);
				$this->assignRef('lists', $lists);

				break;
			default:
				switch($task) {
					/*case 'save':
						$this->get('SaveFilter');
						break;*/
					case 'saveorder':
						$this->get('SaveOrder');
						break;
					case 'remove':
						$this->get('RemoveFilter');
						break;
					case 'publish':
					case 'unpublish':
						$this->get('Publish');
					break;
				}
				/* Get the pagination */
				$pagination = $this->get('Pagination');

				/* Get the tags */
				$filters = $this->get('Filters');

				/* Check if there are any forms */
				$countfilters = $this->get('Total');

				/* Set variabels */
				$this->assignRef('pagination', $pagination);
				$this->assignRef('filters', $filters);
				$this->assignRef('countfilters', $countfilters);

				break;
		}
		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar() {
		switch (JRequest::getCmd('task')) {
			case 'edit':
			case 'apply':
			case 'add':
				switch (JRequest::getCmd('task')) {
					case 'add':
						JToolBarHelper::title(JText::_( 'Add Filter' ), 'redproductfinder_filters');
						break;
					default:
						JToolBarHelper::title(JText::_( 'Edit Filter' ), 'redproductfinder_filters');
						break;
				}
				JToolBarHelper::save();
				JToolBarHelper::apply();
				JToolBarHelper::cancel();
				break;
			default:
				JToolBarHelper::title(JText::_('Filters'), 'redproductfinder_filters');
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList(JText::_('Are you sure you want to delete the filters?'));
				JToolBarHelper::editListX();
				JToolBarHelper::addNew();
				break;
		}
	}
}
?>