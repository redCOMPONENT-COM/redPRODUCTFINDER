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
class RedproductfinderViewTags extends JView {
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
				if ($task == 'apply') $row = $this->get('SaveTag');
				else $row = $this->get('Tag');
				
				if ($row) {
					/* Get the published field */
					$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published);
				}
				else $lists['published'] = '';
				
				/* Get the type names */
				$types = $this->get('Types', 'Types');
				for($k=0;$k<count($types);$k++)
				{
					if($types[$k]->type_select=="Productfinder datepicker")
					{
						unset($types[$k]);
					}
					
				}
				$tagtypes = $this->get('TagTypes');
				if (is_null($tagtypes)) $tagtypes = key($types)+1;
				$lists['types'] = JHTML::_('select.genericlist', $types, 'type_id[]', 'multiple', 'id', 'type_name', $tagtypes);
				
				/* Set variabels */
				$this->assignRef('row', $row);
				$this->assignRef('lists', $lists);
				
				break;
			default:
				switch($task) {
					case 'save':
						$this->get('SaveTag');
						break;
					case 'saveorder':
						$this->get('SaveOrder');
						break;
					case 'remove':
						$this->get('RemoveTag');
						break;
					case 'publish':
					case 'unpublish':
						$this->get('Publish');
					break;
				}
				/* Get the pagination */
				$pagination = $this->get('Pagination');
				
				/* Get the tags */
				$tags = $this->get('Tags');
				
				/* Get the used types */
				$types = $this->get('TagTypeNames');
				
				/* Get the type list */
				$listtypes = $this->get('Types');
				/* Add an all option */
				$dontuse = new StdClass();
				$dontuse->id = '';
				$dontuse->type_name = JText::_('ALL');
				array_unshift($listtypes, $dontuse);
				$lists['types'] = JHTML::_('select.genericlist', $listtypes, 'filtertype', '', 'id', 'type_name', JRequest::getInt('filtertype', ''));
				
				/* Check if there are any forms */
				$counttags = $this->get('Total');
				
				/* Set variabels */
				$this->assignRef('pagination', $pagination);
				$this->assignRef('tags', $tags);
				$this->assignRef('types', $types);
				$this->assignRef('lists', $lists);
				$this->assignRef('counttags', $counttags);
				
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
						JToolBarHelper::title(JText::_( 'Add Tag' ), 'redproductfinder_tags');
						break;
					default:
						JToolBarHelper::title(JText::_( 'Edit Tag' ), 'redproductfinder_tags');
						break;
				}
				JToolBarHelper::save();
				// JToolBarHelper::apply();
				JToolBarHelper::cancel();
				break;
			default:
				JToolBarHelper::title(JText::_('Tags'), 'redproductfinder_tags');
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList(JText::_('Are you sure you want to delete the tags?'));
				JToolBarHelper::editListX();
				JToolBarHelper::addNew();
				break;
		}
	}
}
?>