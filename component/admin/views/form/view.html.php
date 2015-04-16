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
class RedproductfinderViewForm extends JViewLegacy
{
	protected $item;

	protected $form;

	function display( $tpl = null )
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		// $this->state	= $this->get('State');

		$this->addToolbar();

		parent::display($tpl);
	}

	function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		JToolbarHelper::apply('form.apply');
		JToolbarHelper::save('form.save');
		JToolbarHelper::save2new('form.save2new');
		JToolbarHelper::cancel('form.cancel');

		JToolbarHelper::divider();
	}
}
?>
