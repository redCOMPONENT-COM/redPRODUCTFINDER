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
class RedproductfinderViewType extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		JToolbarHelper::apply('type.apply');
		JToolbarHelper::save('type.save');
		JToolbarHelper::save2new('type.save2new');
		JToolbarHelper::cancel('type.cancel');

		JToolbarHelper::divider();
	}
}
?>
