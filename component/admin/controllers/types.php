<?php
/** 
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 *
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

/**
 * Types Controller
 */
class RedproductfinderControllerTypes extends JController {
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct();
		
		/* Redirect templates to templates as this is the standard call */
		$this->registerTask('apply','edit');
		$this->registerTask('add','edit');
		$this->registerTask('save','types');
		$this->registerTask('cancel','types');
		$this->registerTask('remove','types');
		$this->registerTask('saveorder','types');
		$this->registerTask('publish','types');
		$this->registerTask('unpublish','types');
	}
	
	/**
	 * Get the default layout
	 */
	function Types() {
		JRequest::setVar('view', 'types');
		JRequest::setVar('layout', 'types');
		
		parent::display();
	}
	
	/**
	 * Get the edit layout
	 */
	function Edit() {
		JRequest::setVar('view', 'types');
		JRequest::setVar('layout', 'edittype');
		
		parent::display();
	}
}
?>
