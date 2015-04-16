<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER default controller
 */

jimport('joomla.application.component.controller');

/**
 * redPRODUCTFINDER Component Controller
 */
class RedproductfinderController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since   1.6
	 */
	protected $default_view = 'redproductfinder';

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/redproductfinder.php';

		$view   = $this->input->get('view', 'redproductfinder');
		$layout = $this->input->get('layout', 'redproductfinder');
		$id     = $this->input->getInt('id');

		parent::display();

		return $this;
	}
}
?>
