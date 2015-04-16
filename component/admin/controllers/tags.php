<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Tags Controller
 */
class RedproductfinderControllerTags extends JControllerAdmin {
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
		$this->registerTask('save','tags');
		$this->registerTask('cancel','tags');
		$this->registerTask('remove','tags');
		$this->registerTask('saveorder','tags');
		$this->registerTask('publish','tags');
		$this->registerTask('unpublish','tags');
	}

	/**
	 * Get the default layout
	 */
	function Tags() {
		JRequest::setVar('view', 'tags');
		JRequest::setVar('layout', 'tags');

		parent::display();
	}

	/**
	 * Get the edit layout
	 */
	function Edit() {
		/* Create the view */
		$view = $this->getView('tags', 'html');

		/* Add the main model */
		$view->setModel( $this->getModel( 'tags', 'RedproductfinderModel' ), true );

		/* Add extra models */
		$this->addModelPath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redproductfinder' . DS . 'models' );
		$view->setModel( $this->getModel( 'types', 'RedproductfinderModel' ));

		/* Add the layout */
		$view->setLayout('edittag');

		/* Display it all */
		$view->display();
	}
}
?>
