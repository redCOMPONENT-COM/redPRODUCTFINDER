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
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function getExtrafileddata()
	{
		$db = JFactory::getDBO();

		$q = "SELECT * FROM #__redshop_fields "
			."WHERE field_section=17"
			." AND published=1 ";

		$db->setQuery($q);
		$extrafileddata=$db->loadObjectlist();

		return $extrafileddata;
	}

	function display($tpl = null)
	{
		global $mainframe;
		/* Get the task */
		$task = JRequest::getCmd('task');
		// $extradata = $this->getExtrafileddata();

		/* add submenu here */
		RedproductfinderHelper::addSubmenu("types");

		switch ($task) {
			case 'apply':
			case 'edit':
			case 'add':
				if ($task == 'apply') $row = $this->get('SaveType');
				else $row = $this->get('Type');

				if ($row) {
					/* Get the published field */
					$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published);
				}
				else $lists['published'] = '';

				if ($row) {
					/* Get the published field */
					$lists['picker'] = JHTML::_('select.booleanlist',  'picker', 'class="inputbox"', $row->picker,'Month Picker','From-To');
				}
				else $lists['picker'] = '';

				/* Get the list of forms */
				$lists['form_id'] = JHTML::_('select.genericlist', $this->get('Formlist'), 'form_id', '', 'id', 'formname', $row->form_id, false, true);

				/* Set the selector types */
				$generic = new StdClass();
				$generic->select_name = 'generic';
				$checkbox = new StdClass();
				$checkbox->select_name = 'checkbox';
				$productfinderdatepicker = new StdClass();
				$productfinderdatepicker->select_name = 'Productfinder datepicker';
				$type_select = array($generic, $checkbox,$productfinderdatepicker);
				$type_select_extrafield =$extradata;

				$lists['type_select'] = JHTML::_('select.genericlist', $type_select, 'type_select', 'onclick="datepickerValidation(this.value);"', 'select_name', 'select_name', $row->type_select, false, true);

				$lists['extrafield'] = JHTML::_('select.genericlist', $type_select_extrafield, 'extrafield', '', 'field_id', 'field_title', $row->extrafield, false, true);
				/* Set variabels */
				$this->assignRef('row', $row);
				$this->assignRef('lists', $lists);

				break;
			default:
				switch($task) {
					case 'save':
						$this->get('SaveType');
						break;
					case 'saveorder':
						$this->get('SaveOrder');
						break;
					case 'remove':
						$this->get('RemoveType');
						break;
					case 'publish':
					case 'unpublish':
						$this->get('Publish');

					break;
				}
				/* Get the pagination */
				$pagination = $this->get('Pagination');

				/* Get the fields list */
				$types = $this->get('Types');

				/* Check if there are any forms */
				$counttypes = $this->get('Total');

				// $formfilter = $mainframe->getUserStateFromRequest( $context.'formfilter',  'formfilter', 0 );

// 				$temps = array();
// 				$temps[0]->id="0";
// 				$temps[0]->formname=JText::_('SELECT');
// 				$optionsection= @array_merge($temps,$this->get('FormList'));
// 				#$optionsection = $this->get('FormList');
// 				$lists['FormList'] 	= JHTML::_('select.genericlist',$optionsection,  'formfilter', 'class="inputbox" size="1"  onchange="document.adminForm.submit();"' , 'id', 'formname',  $formfilter );

				/* Set variabels */
				$this->assignRef('pagination', $pagination);
				$this->assignRef('types', $types);
				$this->assignRef('lists', $lists);
				$this->assignRef('counttypes', $counttypes);

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
						JToolBarHelper::title(JText::_( 'Add Type' ), 'redproductfinder_type');
						break;
					default:
						JToolBarHelper::title(JText::_( 'Edit Type' ), 'redproductfinder_type');
						break;
				}
				JToolBarHelper::save();
				// JToolBarHelper::apply();
				JToolBarHelper::cancel();
				break;
			default:
				JToolBarHelper::title(JText::_('Types'), 'redproductfinder_type');
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList(JText::_('Are you sure you want to delete the types?'));
				JToolBarHelper::addNew();
				break;
		}
	}
}
?>
