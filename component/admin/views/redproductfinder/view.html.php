<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * ProductSearch View
 */
class RedproductfinderViewRedproductfinder extends JViewLegacy
{
	/**
	 * Productsearch view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		/* Get the total number of tags */
		$stats = $this->get('Totals');

		$this->assignRef('stats', $stats);

		/* add submenu here */
		RedproductfinderHelper::addSubmenu("redproductfinder");

		/* Get the toolbar */
		$this->toolbar();

		/* Add sidebar */
		$this->sidebar = JHtmlSidebar::render();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		JHtmlSidebar::setAction('index.php?option=com_redproductfinder');

		JToolBarHelper::title(JText::_('REDPRODUCTFINDER'), 'redproductfinder_redproductfinder');
		JToolBarHelper::preferences('com_redproductfinder', '300');
	}
}
?>