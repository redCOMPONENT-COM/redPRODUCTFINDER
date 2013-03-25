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

jimport( 'joomla.application.component.view' );

/**
 * redFORM View
 */
class RedproductfinderViewForms extends JView {
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null) {
		global $mainframe;
$mainframe = JFactory::getApplication();
		/* Get the task */
		$task = JRequest::getCmd('task');

		/* Check to see if we have a form name */
		if (strlen(trim(JRequest::getVar('formname'))) == 0 && ($task == 'apply' || $task == 'save')) {
			$row = $this->get('SaveForm');
			$mainframe->redirect('index.php?option=com_redproductfinder&controller=forms&task=edit&cid[]='.$row->id, JText::_('No form name specified'), 'error');
		}

		switch ($task) {
			case 'apply':
			case 'edit':
			case 'add':
				if ($task == 'apply') $row = $this->get('SaveForm');
				else $row = $this->get('Form');

				/* Get the show name option */
				$lists['showname']= JHTML::_('select.booleanlist',  'showname', 'class="inputbox"', $row->showname);

				/* Get the published option */
				
				$lists['published']= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published);
				/* Get the dependency option */
				$lists['dependency']= JHTML::_('select.booleanlist',  'dependency', 'class="inputbox"', $row->dependency);
				/* Set variabels */
				$this->assignRef('row', $row);
				$this->assignRef('lists', $lists);
				break;
			default:
				switch($task) {
					case 'save':
						$this->get('SaveForm');
						break;
					case 'remove':
						$this->get('RemoveForm');
						break;
					case 'publish':
					case 'unpublish':
						$this->get('Publish');
					break;
					case 'clone':
						$this->get('CloneForm');
						break;
					case 'importattributes':
						$this->get('ImportAttributes');
						break;
				}
				/* Get the pagination */
				$pagination = $this->get('Pagination');

				/* Get the competitions list */
				$forms = $this->get('Forms');

				/* Set variabels */
				$this->assignRef('pagination',   $pagination);
				$this->assignRef('forms',   $forms);
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
						JToolBarHelper::title(JText::_( 'Add Form' ), 'redproductfinder_form');
						break;
					default:
						JToolBarHelper::title(JText::_( 'Edit Form' ), 'redproductfinder_form');
						break;
				}
				JToolBarHelper::save();
				JToolBarHelper::apply();
				JToolBarHelper::cancel();
				break;
			default:
				JToolBarHelper::title(JText::_( 'Forms' ), 'redproductfinder_form');
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::custom('clone', 'redvmproductfinder_clone_32', 'redvmproductfinder_clone_32', JText::_('CLONE'), true);
				JToolBarHelper::custom('importattributes', 'redvmproductfinder_importattributes_32', 'redvmproductfinder_importattributes_32', JText::_('IMPORT_ATTRIBUTES'), true);
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList(JText::_('Are you sure you want to delete the form and all related fields and values?'));
				JToolBarHelper::editListX();
				JToolBarHelper::addNew();
				break;
		}
	}
}
?>
