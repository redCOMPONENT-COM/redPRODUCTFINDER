<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Filters controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */
class RedproductfinderControllerFilters extends JControllerAdmin
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'edit');
	}

	/**
	 * Method to get model from table class
	 *
	 * @param   string  $name    Default value is Filter
	 * @param   string  $prefix  Default value is RedproductfinderModel
	 * @param   array   $config  Default value is array
	 *
	 * @return object
	 */
	public function getModel($name = 'Filter', $prefix = 'RedproductfinderModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
