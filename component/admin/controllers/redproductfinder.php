<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * ProductSearch Controller
 */
class RedproductfinderControllerRedproductfinder extends RControllerForm
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct();

		/* Redirect templates to templates as this is the standard call */
		/* $this->registerTask('apply','edit'); */

		JRequest::setVar('view', 'redproductfinder');
		JRequest::setVar('layout', 'redproductfinder');

		parent::display();
	}
}
?>
