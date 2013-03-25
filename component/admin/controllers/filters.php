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
 * Tags Controller
 */
class RedproductfinderControllerFilters extends JController {
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct();
		/* Redirect templates to templates as this is the standard call */
		$this->registerTask('apply','save');
		$this->registerTask('add','edit');

		$this->registerTask('cancel','filters');
		$this->registerTask('remove','filters');
		$this->registerTask('saveorder','filters');
		$this->registerTask('publish','filters');
		$this->registerTask('unpublish','filters');
	}

	/**
	 * Get the default layout
	 */
	function filters() {
		JRequest::setVar('view', 'filters');
		JRequest::setVar('layout', 'filters');

		parent::display();
	}

	/**
	 * Get the edit layout
	 */
	function Edit() {
		/* Create the view */
		$view = $this->getView('filters', 'html');

		/* Add the main model */
		$view->setModel( $this->getModel( 'filters', 'RedproductfinderModel' ), true );

		/* Add extra models */
		$this->addModelPath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redproductfinder' . DS . 'models' );
		$view->setModel( $this->getModel( 'types', 'RedproductfinderModel' ));

		/* Add the layout */
		$view->setLayout('editfilter');

		/* Display it all */
		$view->display();
	}
	function save()
	{
		 global $mainframe;
		$task = JRequest :: getVar('task');
		$cid = JRequest::getVar ( 'id');

		$model = $this->getModel('filters');
		$model->SaveFilter();
		$msg = $model->getError();

		if($task=='apply')
			$mainframe->redirect('index.php?option=com_redproductfinder&task=edit&controller=filters&hidemainmenu=1&cid[]='.$cid,$msg);
		else
			$mainframe->redirect('index.php?option=com_redproductfinder&task=filters&controller=filters',$msg);
	}
}
?>
