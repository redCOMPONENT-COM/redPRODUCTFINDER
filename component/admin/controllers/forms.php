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
 * forms Controller
 */
class RedproductfinderControllerForms extends JControllerAdmin
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct();

		/* Redirect templates to templates as this is the standard call */
		$this->registerTask('save','forms');
		$this->registerTask('remove','forms');
		$this->registerTask('publish','forms');
		$this->registerTask('unpublish','forms');
		$this->registerTask('cancel','forms');
		$this->registerTask('clone','forms');
		$this->registerTask('importattributes','forms');
		$this->registerTask('apply','edit');
	}

	/**
	 * Gets a list of IP/IP ranges in the database
	 */
	function Forms() {
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'forms');

		parent::display();
	}

	/**
	 * Editing a form
	 */
	function Edit() {
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'editform');

		parent::display();
	}

	/**
	 * Adding a form
	 */
	function Add() {
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'editform');

		parent::display();
	}
}
?>
