<?php
/**
 * @copyright  Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license    can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redFORM view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 * redFORM View
 *
 * @package     RedProductfinder
 * @subpackage  View
 * @since       0.9.1
 */
class RedproductfinderViewAssociation extends JViewLegacy
{
	/**
	 * Display edit page
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @todo Check the extra fields once implemented
	 *
	 * @since   0.9.1
	 */
	function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo	We have setup ACL requirements for redITEM
	 *
	 * @return  RToolbar
	 */
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
