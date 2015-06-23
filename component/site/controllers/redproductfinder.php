<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * RedPRODUCTFINDER controller.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class RedproductfinderControllerRedproductfinder extends JControllerForm
{
	/**
	 * Method to display the view
	 *
	 * @access   public
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to show the Redproductfinder view
	 *
	 * @access	public
	 * @return void
	 */
	public function Redproductfinder()
	{
		/* Create the view object */
		$view = $this->getView('redproductfinder', 'html');

		/* Set model paths */
		$this->addModelPath(JPATH_COMPONENT . DS . 'models');

		/* Standard model */
		$view->setModel($this->getModel('Redproductfinder', 'RedproductfinderModel'), true);

		/* Backend models */
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models');
		$view->setModel($this->getModel('types', 'RedproductfinderModel'));
		$view->setModel($this->getModel('tags', 'RedproductfinderModel'));
		$view->setModel($this->getModel('associations', 'RedproductfinderModel'));

		/* Set the layout */
		$view->setLayout('redproductfinder');

		/* Now display the view */
		$view->display();
	}

	/**
	 * Method to show the Redproductfinder view for ajax call
	 *
	 * @access	public
	 * @return void
	 */
	public function Redproductfinder_ajax()
	{
		/* Create the view object */
		$view = $this->getView('redproductfinder', 'html');

		/* Set model paths */
		$this->addModelPath(JPATH_COMPONENT . DS . 'models');

		/* Standard model */
		$view->setModel($this->getModel('Redproductfinder', 'RedproductfinderModel'), true);

		/* Backend models */
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models');
		$view->setModel($this->getModel('types', 'RedproductfinderModel'));
		$view->setModel($this->getModel('tags', 'RedproductfinderModel'));
		$view->setModel($this->getModel('associations', 'RedproductfinderModel'));

		/* Set the layout */
		$view->setLayout('redproductfinder_ajax');

		/* Now display the view */
		$view->display();
	}

	/**
	 * Method to display product
	 *
	 * @return void
	 */
	public function Findproducts()
	{
		/* Set a default view if none exists */
		JRequest::setVar('view', 'redproductfinder');
		JRequest::setVar('layout', 'searchresult');

		parent::display();
	}
}
