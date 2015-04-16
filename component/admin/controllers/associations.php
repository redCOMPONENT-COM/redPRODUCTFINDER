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
 * Tags Controller
 */
class RedproductfinderControllerAssociations extends JControllerAdmin {
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
		$this->registerTask('save','associations');
		$this->registerTask('cancel','associations');
		$this->registerTask('remove','associations');
		$this->registerTask('saveorder','associations');
		$this->registerTask('publish','associations');
		$this->registerTask('unpublish','associations');
	}

	/**
	 * Get the default layout
	 */
	function Associations() {
		JRequest::setVar('view', 'associations');
		JRequest::setVar('layout', 'associations');

		parent::display();
	}

	/**
	 * Get the edit layout
	 */
	function Edit() {
		/* Create the view */
		$view = $this->getView('associations', 'html');

		/* Add the main model */
		$view->setModel( $this->getModel( 'associations', 'RedproductfinderModel' ), true );

		/* Add extra models */
		$this->addModelPath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redproductfinder' . DS . 'models' );
		$view->setModel( $this->getModel( 'tags', 'RedproductfinderModel' ));

		/* Add the layout */
		$view->setLayout('editassociation');

		/* Display it all */
		$view->display();
	}
	/*
	 *  Save dependent tags
	 */
	function savedependent()
	{
		$model = $this->getModel('associations');
		$msg = $model->savedependent();
		echo $msg;
		exit;
	}
}
?>