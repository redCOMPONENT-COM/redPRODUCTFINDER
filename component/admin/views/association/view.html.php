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
class RedproductfinderViewAssociation extends JViewLegacy
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

		JToolbarHelper::apply('association.apply');
		JToolbarHelper::save('association.save');
		JToolbarHelper::save2new('association.save2new');
		JToolbarHelper::cancel('association.cancel');

		JToolbarHelper::divider();
	}
}
?>
