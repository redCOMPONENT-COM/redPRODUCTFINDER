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
class RedproductfinderViewFilter extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolbarHelper::apply('filter.apply');
		JToolbarHelper::save('filter.save');
		JToolbarHelper::save2new('filter.save2new');
		JToolbarHelper::cancel('filter.cancel');

		JToolbarHelper::divider();
	}
}
?>
